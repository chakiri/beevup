<?php

namespace App\Repository;

use App\Entity\ImageGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageGallery[]    findAll()
 * @method ImageGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageGallery::class);
    }

    // /**
    //  * @return ImageGallery[] Returns an array of ImageGallery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageGallery
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function search($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.keyWords LIKE :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
