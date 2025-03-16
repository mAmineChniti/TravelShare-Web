<?php

namespace App\Repository;

use App\Entity\ReservationOffresVoyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationOffresVoyage>
 *
 * @method ReservationOffresVoyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationOffresVoyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationOffresVoyage[]    findAll()
 * @method ReservationOffresVoyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationOffresVoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationOffresVoyage::class);
    }

//    /**
//     * @return ReservationOffresVoyage[] Returns an array of ReservationOffresVoyage objects
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

//    public function findOneBySomeField($value): ?ReservationOffresVoyage
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
