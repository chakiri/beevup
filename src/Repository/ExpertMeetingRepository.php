<?php

namespace App\Repository;

use App\Entity\ExpertMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpertMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertMeeting[]    findAll()
 * @method ExpertMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertMeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertMeeting::class);
    }

    public function findLocal($allCompanies)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->leftJoin('u.company', 'c')
            ->andWhere('c.id in (:companies)')
            ->setParameter('companies', $allCompanies)
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
            ;
    }
}
