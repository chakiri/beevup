<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByValue($value, $allCompanies)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->leftJoin('u.company', 'c')
            ->leftJoin('p.function', 'f')
            ->andWhere('p.firstname LIKE :value')
            ->orWhere('p.lastname LIKE :value')
            ->orWhere('p.introduction LIKE :value')
            ->orWhere('p.mobileNumber LIKE :value')
            ->orWhere('p.phoneNumber LIKE :value')
            ->orWhere('u.email LIKE :value')
            ->orWhere('c.name LIKE :value')
            ->orWhere('f.name LIKE :value')
            ->andWhere('u.company in (:value2)')
            ->andWhere('p.isCompleted = 1')
            ->andWhere('u.isValid = 1')
            ->setParameters(array('value' => '%'.$value.'%', 'value2'=>$allCompanies ));

        return $qb->getQuery()->getResult();
    }

    public function findByIsCompletedProfile($companiesIds)
    {
        $qb = $this->createQueryBuilder('u')
                    ->leftJoin('u.profile', 'p')
                    ->where('p.isCompleted = true')
                    ->andwhere('u.isValid = 1')
                    ->andWhere('u.company in (:companiesIds)')
                    ->setParameters(array('companiesIds'=>$companiesIds ));

        return $qb->getQuery()->getResult();
    }

    public function findByStore( $store)
    {
        $qb = $this->createQueryBuilder('u')
           ->where('u.company is NULL')
           ->andWhere('u.store = :value')
           ->andWhere('u.isValid = 1')
           ->setParameters(array('value' => $store));
        return $qb->getQuery()->getResult();
    }

    public function findByAdminOfStore($store, $role)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.store = :value1')
            ->andWhere('u.roles LIKE :value2')
            ->andwhere('u.isValid = 1')
            ->setParameters(array('value1' => $store, 'value2' => '%"'.$role.'"%'));
        return $qb->getQuery()->getResult();
    }

    public function findByAdminCompany($companyId){
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.type', 't')
            ->where('u.company = :companyId')
            ->andWhere('t.id = 3')
            ->setParameter('companyId', $companyId);
        return $qb->getQuery()->getOneOrNullResult();

    }
    public function findAdvisersOfStore( $store , $type, $type2, $type3 )
    {
        $result = array_merge($type, $type2, $type3 );
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->where('u.store = :value1')
            ->andwhere('u.isValid = 1')
            ->andWhere('u.type IN (:value2)')
            ->andWhere('p.firstname IS NOT NULL')
            ->setParameters(array('value1' => $store, 'value2'=> $result));
       return $qb->getQuery()->getResult();
    }

    public function findByTopic($topicName)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.topics', 't')
            ->where('t.name = :name')
            ->setParameter('name', $topicName)
        ;

        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
