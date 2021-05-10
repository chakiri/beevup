<?php

namespace App\Repository;

use App\Entity\ExpertMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpertMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertMeeting[]    findAll()
 * @method ExpertMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertMeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertMeeting::class);
    }

    // /**
    //  * @return ExpertMeeting[] Returns an array of ExpertMeeting objects
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
    public function findOneBySomeField($value): ?ExpertMeeting
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
