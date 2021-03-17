<?php

namespace App\Repository;

use App\Entity\UserFunction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method UserFunction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFunction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFunction[]    findAll()
 * @method UserFunction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFunctionRepository extends ServiceEntityRepository
{

    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, UserFunction::class);
        $this->security = $security;
    }

    public function getListFunctionsUser()
    {
        $user = $this->security->getUser();

        $q = $this->createQueryBuilder('t')
            ->where('t.relatedTo =  :val')
            ->orderBy('t.name', 'ASC');

        if($user->getCompany()) {
            $q->setParameter('val', 'Company');
        }else{
            $q->setParameter('val', 'Store');
        }

        return $q;
    }

    // /**
    //  * @return UserFunction[] Returns an array of UserFunction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserFunction
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
