<?php

namespace App\Repository;

use App\Entity\Hotels;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Hotels>
 *
 * @method Hotels|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hotels|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hotels[]    findAll()
 * @method Hotels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotels::class);
    }

    //    /**
    //     * @return Hotels[] Returns an array of Hotels objects
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

    //    public function findOneBySomeField($value): ?Hotels
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
