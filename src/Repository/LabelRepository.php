<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    public function labelsRequestQueryBuilder($storeId)
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.company', 'c')
            ->leftJoin('c.store', 's')
            ->andWhere('s.id = :storeId')
            ->andWhere('l.charter = true')
            ->andWhere('l.kbisStatus is not null')
            ->andWhere('l.isLabeled = false')
            ->orderBy('l.createdAt', 'DESC')
            ->setParameter('storeId', $storeId)
            ;

        return $qb;
    }

    public function labelsRequest($storeId)
    {
        $qb = $this->labelsRequestQueryBuilder($storeId);

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
