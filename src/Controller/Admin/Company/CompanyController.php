<?php
namespace App\Controller\Admin\Company;
use App\Repository\CompanyRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class CompanyController extends EasyAdminController
{
    private $userRepo;
    private $postRepo;
    private $companyRepo;

    public function __construct( UserRepository $userRepo, PostRepository $postRepo, CompanyRepository $companyRepo)
    {
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->companyRepo = $companyRepo;
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
}