<?php

namespace App\Repository;

use App\Entity\ServiceCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceCategory[]    findAll()
 * @method ServiceCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceCategory::class);
    }

    public function findAllMatching(?string $query, int $limit)
    {
        $q = $this->createQueryBuilder('c')
            ->andWhere('c.isWaiting = false')
            ->setMaxResults($limit)
            ;

        if ($query){
            $q
                ->andWhere('c.name LIKE :query')
                ->setParameter('query', '%'.$query.'%')
                ;
        }

        return $q
            ->getQuery()
            ->getResult()
            ;
    }
}
