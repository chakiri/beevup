<?php

namespace App\Repository;

use App\Entity\PostLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostLike::class);
    }

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    
    /*public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }*/

    public function findByPost($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.post = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

    
    public function findOneByPostAndUser($value1,$value2): ?PostLike
    {
         return $this->createQueryBuilder('l')
            ->andWhere('l.user = :val1')
            ->andWhere('l.post = :val2')
            ->setParameters(array('val1' => $value1,'val2' => $value2))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
