<?php

namespace App\Repository;

use App\Entity\Reclamations;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reclamations>
 *
 * @method Reclamations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamations[]    findAll()
 * @method Reclamations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamations::class);
    }

    //    /**
    //     * @return Reclamations[] Returns an array of Reclamations objects
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

    //    public function findOneBySomeField($value): ?Reclamations
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
