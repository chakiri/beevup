<?php

namespace App\Repository;

use App\Entity\OpportunityNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OpportunityNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpportunityNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpportunityNotification[]    findAll()
 * @method OpportunityNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpportunityNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpportunityNotification::class);
    }

    public function findByLastMonthNotification($value)
    {


        $from =   date("Y-m-d H:i:s");
        $to = date("Y-m-d H:i:s",strtotime("-1 month"));

        return $this->createQueryBuilder('p')
            ->where('p.user = :val')
            ->andWhere('p.createdAt BETWEEN :to AND :from')
            ->setParameters(array('val' => $value,'from'=>$from, 'to'=>$to ))
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return OpportunityNotification[] Returns an array of OpportunityNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OpportunityNotification
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
