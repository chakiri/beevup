<?php

namespace App\Repository;

use App\Entity\StoreService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoreService|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreService|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreService[]    findAll()
 * @method StoreService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreService::class);
    }

    public function findByStore($store)
    {
        return $this->createQueryBuilder('s')
            ->select('s.service')
            ->from('App\Entity\StoreService', 's')
            ->andWhere('s.store = :store')
            ->setParameter('store', $store)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByIsDiscovery($store){

        return $this->createQueryBuilder('t')
            ->leftJoin('t.service', 's')
            ->andWhere('t.store = :store')
            ->andWhere('s.isDiscovery= 1')
            ->andWhere('s.type = 1')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('store', $store)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return StoreService[] Returns an array of StoreService objects
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
    public function findOneBySomeField($value): ?StoreService
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
