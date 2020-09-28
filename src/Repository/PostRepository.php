<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Post::class);
        $this->security = $security;
    }

    
    public function findOneById($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOpportunitiesByDate($user, $date){
        $qb = $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.user != :user')
            ->andWhere('p.createdAt >= :date')
            ->setParameter('category', 2)
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ;

        return $qb->getQuery()
            ->getResult()
            ;
    }

    public function findByNotSeenOpportunityPost($value, $value2, $value3)
    {

        $from =   date("Y-m-d H:i:s");
        $to = date("Y-m-d H:i:s",strtotime("-1 month"));

       return $this->createQueryBuilder('p')
           ->where('p.category = :val')
           ->andWhere('p.createdAt BETWEEN :to AND :from')
           ->andWhere('p.user != :val3')
           ->andWhere('p.id NOT IN (:val2)')
           ->setParameters(array('val' => $value,'val2'=>$value2,'val3'=>$value3, 'from'=>$from, 'to'=>$to ))
           ->getQuery()
           ->getResult();


    }

    public function findByStore()
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->addSelect('u')
            ->andWhere('u.store = :store')
            ->setParameter('store', $this->security->getUser()->getStore())
        ;

        return $qb;
    }

    public function findByNotReportedPosts()
    {
        //Get local content
        $qb = $this->findByStore();

        $qb
            ->andWhere('p.status IS NULL')
            ->orWhere('p.status = 1')
            ->orderBy('p.createdAt', 'DESC')
            ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByCategory($category)
    {
        //Get local content
        $qb = $this->findByStore();

        $qb
            ->andWhere('p.status IS NULL')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.createdAt', 'DESC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByIds($value1, $value2)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id between :val1 and :val2')
            ->setParameter('val', $value1)
            ->setParameters(array('val1' => $value1,'val2'=>$value2 ))
            ->getQuery()
            ->getArrayResult();

    }

}
