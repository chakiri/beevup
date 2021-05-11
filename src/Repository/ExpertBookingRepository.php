<?php

namespace App\Repository;

use App\Entity\ExpertBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpertBooking|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertBooking|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertBooking[]    findAll()
 * @method ExpertBooking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertBookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertBooking::class);
    }

    // /**
    //  * @return ExpertBooking[] Returns an array of ExpertBooking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpertBooking
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
