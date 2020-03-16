<?php

namespace App\Repository;

use App\Entity\UserFunction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserFunction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFunction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFunction[]    findAll()
 * @method UserFunction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFunctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFunction::class);
    }

    // /**
    //  * @return UserFunction[] Returns an array of UserFunction objects
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
    public function findOneBySomeField($value): ?UserFunction
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
