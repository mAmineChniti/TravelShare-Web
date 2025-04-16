<?php

namespace App\Repository;

use App\Entity\Excursions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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