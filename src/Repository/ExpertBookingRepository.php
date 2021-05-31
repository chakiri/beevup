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

    public function findByMeetingQueryBuilder($expertMeeting): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.expertMeeting = :expertMeeting')
            ->setParameter('expertMeeting', $expertMeeting)
            ->orderBy('e.createdAt', 'DESC')
            ;

        return $qb;
    }

    public function findByStatus($expertMeeting, $status)
    {
        $qb = $this->findByMeetingQueryBuilder($expertMeeting);

        return $qb
            ->andWhere('e.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByMeeting($expertMeeting)
    {
        $qb = $this->findByMeetingQueryBuilder($expertMeeting);

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
