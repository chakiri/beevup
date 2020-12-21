<?php

namespace App\Repository;

use App\Entity\BeContacted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BeContacted|null find($id, $lockMode = null, $lockVersion = null)
 * @method BeContacted|null findOneBy(array $criteria, array $orderBy = null)
 * @method BeContacted[]    findAll()
 * @method BeContacted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeContactedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BeContacted::class);
    }

}