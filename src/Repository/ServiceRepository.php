<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    private function findSearchQueryBuilder($query, $category, $isDiscovery)
    {
        $q = $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('u.company', 'c')
        ;

        if ($query){
            $q->andWhere('s.title LIKE :query')
                ->orWhere('s.introduction LIKE :query')
                ->orWhere('s.description LIKE :query')
                ->setParameter('query', '%' .$query . '%')
            ;
        }

        if ($category)
            $q->andWhere('s.category = :category')
                ->setParameter('category', $category)
            ;

        if ($isDiscovery)
            $q->andWhere('s.isDiscovery = :isDiscovery')
                ->setParameter('isDiscovery', $isDiscovery)
            ;

        return $q;
    }

    public function findSearch($query, $category, $isDiscovery, $allCompanies)
    {
        $q = $this->findSearchQueryBuilder($query, $category, $isDiscovery);

        $q->andWhere('c.id in (:companies)')
            ->andWhere('c.isValid = 1')
            ->setParameter('companies', $allCompanies)
            ->orderBy('s.createdAt', 'DESC')
        ;

        return $q->getQuery()
            ->getResult()
            ;
    }

    public function findSearchStoreServices($storeServices, $query, $category, $isDiscovery)
    {
        $servicesStoreMatchedQuery = [];
        foreach($storeServices as $storeService){
            $q = $this->findSearchQueryBuilder($query, $category, $isDiscovery);
            $q->andWhere('s.id = :idService')
                ->setParameter('idService', $storeService->getService()->getId())
                ;
            $serviceMatchedQuery = $q->getQuery()
                ->getOneOrNullResult()
            ;

            if ($serviceMatchedQuery){
                array_push($servicesStoreMatchedQuery, $serviceMatchedQuery);
            }
        }
        return $servicesStoreMatchedQuery;
    }

    public function findByLocalServicesQuery($allCompanies){

        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('u.company', 'c')
            ->Where('c.id IN  (:companies)')
            ->andWhere('c.isValid = true')
            ->andWhere('c.isCompleted = true')
            ->orderBy('s.createdAt', 'DESC')
            ->addOrderBy('s.isDiscovery', 'DESC')
            ->setParameters(array('companies'=>$allCompanies))
            ;
    }

    public function findByLocalServices($allCompanies){

        $qb = $this->findByLocalServicesQuery($allCompanies);

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByLocalServicesWithLimit($allCompanies, $limit){

        $qb = $this->findByLocalServicesQuery($allCompanies);

        return $qb
            ->getQuery()
            ->setMaxResults( $limit )
            ->getResult()
            ;
    }

    public function findByIsDiscoveryQuery($allCompanies, $store)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('u.company', 'c')
            ->andWhere('s.isDiscovery = 1')
            ->andWhere('c.id in (:companies)')
            ->andWhere('c.isValid = 1')
            ->orWhere('u.company is NULL AND u.store = :store AND s.isDiscovery = 1')
            ->orderBy('s.createdAt', 'DESC')
            ->setParameters(array('companies'=>$allCompanies, 'store'=>$store))
            ;
    }

    public function findByIsDiscovery($allCompanies, $store)
    {
        $query = $this->findByIsDiscoveryQuery($allCompanies, $store);

        return $query
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByIsDiscovery( $allCompanies, $store)
    {
        $query = $this->findByIsDiscoveryQuery($allCompanies, $store);

        return $query
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByType($type){

        return $this->createQueryBuilder('s')
                ->leftJoin('s.user', 'u')
                ->leftJoin('u.company', 'c')
                ->andWhere('s.type = :type')
                ->andWhere('c.isValid = 1')
                ->orderBy('s.createdAt', 'DESC')
                ->setParameters(array('type'=> $type))
                ->getQuery()
                ->getResult() ;

        }

    public function findByCategory($category, $allCompanies, $serviceID){

        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('u.company', 'c')
            ->Where('s.category = :category')
            ->andWhere('c.id in (:companies)')
            ->andWhere('c.isValid = 1')
            ->andWhere('s.id != (:serviceID)')
            ->orderBy('s.createdAt', 'DESC')
            ->setParameters(array('category'=> $category, 'companies'=>$allCompanies, 'serviceID'=>$serviceID))
            ->setMaxResults(3)
            ->getQuery()
            ->getResult() ;
    }

    public function findByQuery($allCompanies, $query)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('u.company', 'c')
            ->andWhere('c.id in (:allCompanies)')
            ->andWhere('c.isCompleted = true')
            ->andWhere('c.isValid = true')
            ->andWhere('s.title LIKE :query OR s.introduction LIKE :query OR s.description LIKE :query')
            ->orderBy('s.createdAt', 'DESC')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('allCompanies', $allCompanies)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findModel($type, $query)
    {
        $q = $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->andWhere('s.title LIKE :query OR s.introduction LIKE :query OR s.description LIKE :query')
            ->setParameter('type', $type)
            ->setParameter('query', '%' . $query . '%')
            ;

        return $q
            ->getQuery()
            ->getResult()
            ;
    }
}
