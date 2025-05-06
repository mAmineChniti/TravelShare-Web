<?php

namespace App\Repository;

use App\Entity\ReservationPacks;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ReservationPacks>
 *
 * @method ReservationPacks|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationPacks|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationPacks[]    findAll()
 * @method ReservationPacks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationPacksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationPacks::class);
    }

    //    /**
    //     * @return ReservationPacks[] Returns an array of ReservationPacks objects
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

    //    public function findOneBySomeField($value): ?ReservationPacks
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
