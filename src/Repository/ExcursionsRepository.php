<?php

namespace App\Repository;

use App\Entity\Excursions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Excursions>
 *
 * @method Excursions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Excursions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Excursions[]    findAll()
 * @method Excursions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExcursionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Excursions::class);
    }

//    /**
//     * @return Excursions[] Returns an array of Excursions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Excursions
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
