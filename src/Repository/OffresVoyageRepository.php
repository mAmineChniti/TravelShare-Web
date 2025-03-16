<?php

namespace App\Repository;

use App\Entity\OffresVoyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffresVoyage>
 *
 * @method OffresVoyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffresVoyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffresVoyage[]    findAll()
 * @method OffresVoyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffresVoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffresVoyage::class);
    }

//    /**
//     * @return OffresVoyage[] Returns an array of OffresVoyage objects
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

//    public function findOneBySomeField($value): ?OffresVoyage
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
