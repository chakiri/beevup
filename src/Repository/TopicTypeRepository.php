<?php

namespace App\Repository;

use App\Entity\TopicType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TopicType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicType[]    findAll()
 * @method TopicType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicType::class);
    }

    // /**
    //  * @return TopicType[] Returns an array of TopicType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TopicType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
