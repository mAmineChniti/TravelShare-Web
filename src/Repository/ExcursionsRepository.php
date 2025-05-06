<?php

namespace App\Repository;

use App\Entity\Excursions;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ExcursionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Excursions::class);
    }

    public function findAllWithGuides()
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.guide', 'g')
            ->addSelect('g')
            ->getQuery()
            ->getResult();
    }
}
