<?php

namespace App\Repository;

use App\Entity\ScorePoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScorePoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScorePoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScorePoint[]    findAll()
 * @method ScorePoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScorePointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScorePoint::class);
    }

    // /**
    //  * @return ScorePoint[] Returns an array of ScorePoint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScorePoint
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
