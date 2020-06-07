<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function findByValue($value)
    {

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->where('p.email = :value')
            ->orWhere('p.siret LIKE :value')
            ->orWhere('p.phone LIKE :value')
            ->orWhere('p.city LIKE :value')
            ->orWhere('p.country LIKE :value')
            ->orWhere('p.name LIKE :value')
            ->orWhere('p.introduction LIKE :value')
            ->orWhere('p.description LIKE :value')
            ->orWhere('p.video LIKE :value')
            ->orWhere('p.addressPostCode LIKE :value')
            ->orWhere('p.addressStreet LIKE :value')
            ->orWhere('p.addressNumber LIKE :value')
            ->orWhere('p.website LIKE :value')
            ->orWhere('p.otherCategory LIKE :value')
            ->orWhere('c.name LIKE :value')
            ->andWhere('p.isCompleted = true')
            ->setParameters(array('value' => '%'.$value.'%'));

        return $qb->getQuery()->getResult();
    }
    public function findByValueAndCategory($value, $value2)
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

            ->setParameters(array('value' => $value, 'value2'=>$value2));


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

    
    // /**
    //  * @return Company[] Returns an array of Company objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    
    
}
