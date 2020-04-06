<?php

namespace App\Repository;

use App\Entity\DashboardNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DashboardNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method DashboardNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method DashboardNotification[]    findAll()
 * @method DashboardNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DashboardNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardNotification::class);
    }
    public function findOneByPostAndUser($value1,$value2,$value3): ?DashboardNotification
    {
         return $this->createQueryBuilder('l')
            ->andWhere('l.user = :val1')
            ->andWhere('l.post = :val2')
            ->andWhere('l.type = :val3')
            ->setParameters(array('val1' => $value1,'val2' => $value2,'val3' => $value3))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByDistinctPostAndType($value1)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.owner = :val1')
            ->andWhere('u.seen = :val2')
            ->groupBy('u.id', 'u.post, u.type')
            ->setParameters(array('val1' => $value1,'val2' => 0))
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByPost($value1)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.post = :val1')
            ->setParameters(array('val1' => $value1))
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return DashboardNotification[] Returns an array of DashboardNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findOneByComment($value): ?DashboardNotification
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.comment = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
