<?php

namespace App\Repository;

use App\Entity\MessageNotification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MessageNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageNotification[]    findAll()
 * @method MessageNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageNotification::class);
    }

    public function findMessageNotifs(User $user)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->andWhere('n.nbMessages != :value')
            ->setParameter('user', $user)
            ->setParameter('value', 0)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return MessageNotification[] Returns an array of MessageNotification objects
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
    public function findOneBySomeField($value): ?MessageNotification
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
