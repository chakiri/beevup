<?php

namespace App\Repository;

use App\Entity\Recommandation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ServiceRepository;
/**
 * @method Recommandation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recommandation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recommandation[]    findAll()
 * @method Recommandation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecommandationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recommandation::class);
    }
    public function findOneById($value): ?Recommandation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserRecommandation($currentUser, $status)
    {
        $qb = $this->createQueryBuilder('d')
        ->leftJoin('d.service', 's')
        ->leftJoin('s.user', 'u')
        ->where('d.status = :status')
        ->andWhere('u.id = :currentUser')
        ->setParameters(array('status' => $status,'currentUser' => $currentUser));
        return $qb->getQuery()->getResult();
    }

    public function findByCompanyRecommandation($company, $status)
    {
        $qb = $this->createQueryBuilder('d')
        ->leftJoin('d.service', 's')
        ->leftJoin('s.user', 'u')
        ->where('d.status = :status')
        ->andWhere('u.company = :company')
        ->setParameters(array('status' => $status,'company' => $company));
        return $qb->getQuery()->getResult();
    }

    public function findByStoreServices($store, $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->andWhere('r.store = :store')
            ->andWhere('r.service is not null')
            ->setParameters(array('status' => $status,'store' => $store))
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByStoreWithoutServices($store, $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->andWhere('r.store = :store')
            ->andWhere('r.service is null')
            ->setParameters(array('status' => $status,'store' => $store))
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByCompanyServices($company, $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->andWhere('r.company = :company')
            ->andWhere('r.service is not null')
            ->setParameters(array('status' => $status,'company' => $company))
        ;

        return $qb->getQuery()->getResult();
    }

    public function findByCompanyWithoutServices($company, $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->andWhere('r.company = :company')
            ->andWhere('r.service is null')
            ->setParameters(array('status' => $status,'company' => $company))
        ;

        return $qb->getQuery()->getResult();
    }


    // /**
    //  * @return Recommandation[] Returns an array of Recommandation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    
    
}
