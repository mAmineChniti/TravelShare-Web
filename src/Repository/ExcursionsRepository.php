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

    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('e')
                   ->leftJoin('e.guide', 'g')
                   ->addSelect('g');

        if (!empty($criteria['title'])) {
            $qb->andWhere('e.title LIKE :title')
               ->setParameter('title', '%'.$criteria['title'].'%');
        }

        if (!empty($criteria['max_price'])) {
            $qb->andWhere('e.prix <= :maxPrice')
               ->setParameter('maxPrice', $criteria['max_price']);
        }

        return $qb->getQuery()->getResult();
    }
}
