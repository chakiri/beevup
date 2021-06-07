<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    public function findExistingSlot($date, $startTime)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.timeSlot', 'ts')
            ->andWhere('ts.date = :date')
            ->andWhere('s.startTime = :startTime')
            ->setParameters(['date' => $date, 'startTime' => $startTime])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
