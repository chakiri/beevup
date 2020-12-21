<?php

namespace App\Repository;

use App\Entity\Sponsorship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sponsorship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sponsorship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sponsorship[]    findAll()
 * @method Sponsorship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsorshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsorship::class);
    }

    // /**
    //  * @return Sponsorship[] Returns an array of Sponsorship objects
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
    public function findOneBySomeField($value): ?Sponsorship
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
