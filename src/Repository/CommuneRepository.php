<?php

namespace App\Repository;

use App\Entity\Commune;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commune|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commune|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commune[]    findAll()
 * @method Commune[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommuneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commune::class);
    }

    public function getCommunes($query, $limit){
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.postalCode LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query',  '%' . $query . '%')
         ;

        return $qb->getQuery()
            ->setMaxResults($limit)
            ->getResult()
            ;
    }
}
