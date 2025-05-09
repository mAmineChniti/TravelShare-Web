<?php

namespace App\Repository;

use App\Entity\Guides;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Guides>
 *
 * @method Guides|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guides|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guides[]    findAll()
 * @method Guides[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuidesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guides::class);
    }

    //    /**
    //     * @return Guides[] Returns an array of Guides objects
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

    //    public function findOneBySomeField($value): ?Guides
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
