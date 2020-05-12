<?php

namespace App\Repository;

use App\Entity\Publicity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publicity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicity[]    findAll()
 * @method Publicity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicity::class);
    }

    // /**
    //  * @return Publicity[] Returns an array of Publicity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Publicity
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
