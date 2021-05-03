<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }
    public function findOneById($value): ?Company
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByValue($value, $allCompanies, $store)
    {

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->where('p.email = :value')
            ->orWhere('p.siret LIKE :value')
            ->orWhere('p.phone LIKE :value')
            ->orWhere('p.city LIKE :value')
            ->orWhere('p.country LIKE :value')
            ->orWhere('p.name LIKE :value')
            ->orWhere('p.description LIKE :value')
            ->orWhere('p.video LIKE :value')
            ->orWhere('p.addressPostCode LIKE :value')
            ->orWhere('p.addressStreet LIKE :value')
            ->orWhere('p.addressNumber LIKE :value')
            ->orWhere('p.website LIKE :value')
            ->orWhere('p.otherCategory LIKE :value')
            ->orWhere('c.name LIKE :value')
            ->andWhere('p.id in (:value2)')
            ->andWhere('p.isCompleted = true')
            ->setParameters(array('value' => '%'.$value.'%', 'value2'=>$allCompanies));

        return $qb->getQuery()->getResult();
    }

    public function findByValueAndCategory($value, $value2, $value3)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->where('p.email = :value')
            ->orWhere('p.siret = :value')
            ->orWhere('p.phone = :value')
            ->orWhere('p.city = :value')
            ->orWhere('p.country = :value')
            ->andWhere('p.isCompleted = true')
            ->andWhere('c.name LIKE  :value2')
            ->andWhere('c.id in  (:value3)')

            ->setParameters(array('value' => $value, 'value2'=>$value2, 'value3'=>$value3));

        return $qb->getQuery()->getResult();
    }

    public function findByCategory($value)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.comapnyCatgory', 'c')
            ->where('c.name = :value')
            ->setParameters(array('value' => $value));
        return $qb->getQuery()->getResult();
    }


    public function findByCompaniesInCommunity($currentStore, $allCompanies)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id in  :value2')
            ->orWhere('c.store =  :val')
            ->andWhere('c.isCompleted = true')
            ->setParameters(array('val' => $currentStore, 'value2'=>$allCompanies))
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    //Get companies from search
    public function findBySearch($name, $allCompanies)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.category', 'ctg')
            /*->leftJoin('c.label', 'l')*/
            ->andWhere('c.id in (:allCompanies)')
            ->andWhere('c.isCompleted = true')
            ->andWhere('c.isValid = true')
            /*->orderBy('l.isLabeled', 'DESC')*/
            ->setParameter('allCompanies', $allCompanies)
            ;
        if ($name != ''){
            $qb->andWhere('c.name LIKE :namelike OR c.email = :name OR c.siret = :name OR c.phone = :name OR c.city = :name OR c.country = :name OR ctg.name LIKE :namelike OR c.otherCategory LIKE :namelike')
                /*->orWhere('c.introduction LIKE :namelike')
                ->orWhere('c.email = :name')
                ->orWhere('c.siret = :name')
                ->orWhere('c.phone = :name')
                ->orWhere('c.city = :name')
                ->orWhere('c.country = :name')*/
                ->setParameter('namelike', '%' . $name . '%')
                ->setParameter('name', $name)
            ;
        }

        return $qb->getQuery()->getResult();
    }


    // get premuim companies
    public function findByPremuimCompanies($offerType)
    {
        $currentDate = date("m.d.y");
        return $this->createQueryBuilder('c')
            ->leftJoin('c.subscription', 's')
            ->andWhere('s.type = :val')
            ->andWhere('s.endDare =< :val2')
            ->setParameters(array('val' => $offerType, 'val2' => $currentDate))
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    // get companies full premuim
    public function findByFullPremuimCompanies()
    {
        $currentDate = date("m.d.y");
        return $this->createQueryBuilder('c')
            ->leftJoin('c.subscription', 's')
            ->andWhere('s.type = :val')
            ->andWhere('s.endDare =< :val2')
            ->setParameters(array('val' => 'Full Premuim', 'val2' => $currentDate))
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getCompaniesObjects($allCompanies): array
    {
        $companies = [];
        foreach($allCompanies as $companyId){
            $company = $this->createQueryBuilder('c')
                ->andWhere('c.id = :id')
                ->setParameter('id', $companyId)
                ->getQuery()
                ->getResult()
            ;
            $companies = array_merge($companies, $company);
        }

        return $companies;
    }

    
    
    
}
