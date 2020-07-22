<?php

namespace App\Repository;

use App\Entity\UserHistoric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserHistoric|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserHistoric|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserHistoric[]    findAll()
 * @method UserHistoric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserHistoricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserHistoric::class);
    }

    // /**
    //  * @return UserHistoric[] Returns an array of UserHistoric objects
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
    public function findOneBySomeField($value): ?UserHistoric
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
