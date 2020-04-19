<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByValue($value)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.profile', 'p')
            ->leftJoin('u.company', 'c')
            ->where('p.firstname LIKE :value')
            ->orWhere('p.lastname LIKE :value')
            ->orWhere('u.email LIKE :value')
            ->orWhere('c.name LIKE :value')
            ->andWhere('p.isCompleted = 1')
            ->setParameters(array('value' => '%'.$value.'%'));

        return $qb->getQuery()->getResult();
    }
    public function findByIsCompletedProfile()
    {
        $qb = $this->createQueryBuilder('u')
                    ->leftJoin('u.profile', 'p')
                    ->where('p.isCompleted = true');

        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
