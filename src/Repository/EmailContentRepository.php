<?php

namespace App\Repository;

use App\Entity\EmailContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailContent[]    findAll()
 * @method EmailContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailContent::class);
    }

    // /**
    //  * @return EmailContent[] Returns an array of EmailContent objects
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
    public function findOneBySomeField($value): ?EmailContent
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
