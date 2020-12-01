<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * @param $allCompanies
     * @param $query
     * @return int|mixed|string
     */
    public function findByQuery($allCompanies, $query)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->leftJoin('u.company', 'c')
            ->andWhere('c.id in (:companies)')
            ->andWhere('c.isValid = 1')

            ->andWhere('p.firstname LIKE :query OR p.lastname LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('companies', $allCompanies)
            ->getQuery()
            ->getResult()
            ;
    }
}
