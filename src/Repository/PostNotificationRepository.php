<?php

namespace App\Repository;

use App\Entity\PostNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PostNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostNotification[]    findAll()
 * @method PostNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostNotification::class);
    }
    public function findOneByPostAndUser($value1,$value2,$value3): ?PostNotification
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
            ->andWhere('u.user != :val1')
            ->groupBy('u.id, u.post, u.type')
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
    //  * @return PostNotification[] Returns an array of PostNotification objects
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

    
    public function findOneByComment($value): ?PostNotification
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.comment = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
