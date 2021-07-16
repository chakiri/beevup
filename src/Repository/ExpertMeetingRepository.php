<?php

namespace App\Repository;

use App\Entity\ExpertMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method ExpertMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpertMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpertMeeting[]    findAll()
 * @method ExpertMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpertMeetingRepository extends ServiceEntityRepository
{
    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, ExpertMeeting::class);
        $this->security = $security;
    }

    public function findLocalQueryBuilder($allCompanies)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.user', 'u')
            ->leftJoin('u.company', 'c')
            ->andWhere('c.id in (:companies)')
            ->setParameter('companies', $allCompanies)
            ->orderBy('e.createdAt', 'DESC')
            ;
    }

    public function findLocal($allCompanies)
    {
        $qb = $this->findLocalQueryBuilder($allCompanies);

        /*$qb
            ->andWhere('u.id != :userId')
            ->setParameter('userId', $this->security->getUser()->getId())
            ;*/

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLastsExpertMeeting($limit){
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
           /* ->setMaxResults($limit)*/
            ->getQuery()
            ->getResult()
            ;
    }
}
