<?php

namespace App\Repository;

use App\Entity\ExpertBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpertBooking|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertBooking|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertBooking[]    findAll()
 * @method ExpertBooking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertBookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertBooking::class);
    }

    public function findByMeeting($expertMeeting): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.expertMeeting = :expertMeeting')
            ->setParameter('expertMeeting', $expertMeeting)
            ;

        return $qb;
    }

    public function findByStatus($expertMeeting, $status)
    {
        $qb = $this->findByMeeting($expertMeeting);

        return $qb
            ->andWhere('e.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }
}
