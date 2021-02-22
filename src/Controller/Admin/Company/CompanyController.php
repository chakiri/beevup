<?php
namespace App\Controller\Admin\Company;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Service\Export\CsvExporter;

class CompanyController extends EasyAdminController
{
    private $userRepo;
    private $postRepo;
    private $companyRepo;
    private $csvExporter;

    public function __construct( UserRepository $userRepo, PostRepository $postRepo, CompanyRepository $companyRepo, CsvExporter $csvExporter)
    {
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->companyRepo = $companyRepo;
        $this->csvExporter = $csvExporter;
    }
    public function changeStatusAction()
    {
        $id = $this->request->query->get('id');
        $page = $this->request->query->get('page');

        $company = $this->companyRepo->findOneBy(['id'=>$id]);
        $Status = ($company->isValid()) ? false :true;
        $company->setIsValid($Status);
        $this->em->flush();

        $users = $this->userRepo->findBy(['company'=>$id]);


        foreach ($users as $user)
        {

            $user->setIsValid($Status);
            $this->em->flush();
            $posts =  $this->postRepo->findBy(['user'=>$user]);
            foreach ($posts as $post)
            {

                $post->setStatus($Status);
                $this->em->flush();
            }


        }

        return $this->redirectToRoute('easyadmin', [
            'action' => 'list',
            'entity' => $this->request->query->get('entity'),
            'id' => $id,
            'page' =>$page
        ]);

    }

    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        if($this->getUser()->getId() != 1) {
            $dqlFilter = sprintf('entity.store = %s', $store->getId());
        }
        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
        return $list;
    }

    public function exportCompanyAction()
    {
        $sortDirection = $this->request->query->get('sortDirection');
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), ['ASC', 'DESC'])) {
            $sortDirection = 'DESC';
        }
        $queryBuilder = $this->createListQueryBuilder(
            $this->entity['class'],
            $sortDirection,
            $this->request->query->get('sortField'),
            $this->entity['list']['dql_filter']
        );
        return $this->csvExporter->getResponseFromQueryBuilder(
            $queryBuilder,
            Company::class,
            'Entreprises.csv'
        );
    }

    protected function createSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        $qb = parent::createSearchQueryBuilder($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);
        if ($entityClass === Company::class) {
            $qb->innerJoin('entity.users', 'u')
               ->innerJoin('u.profile', 'p')
               ->innerJoin('u.type', 't')
                ->orWhere('LOWER(p.firstname) LIKE :search or LOWER(p.lastname) LIKE :search')
                ->orWhere('u.email = :search')
                ->andWhere('entity.store = :store')
                ->setParameter('search','%'.$searchQuery.'%')
                ->setParameter('store',$store)
            ;
        }
        return $qb;
    }

}