<?php

namespace App\Repository;

use App\Entity\Chambres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chambres>
 *
 * @method Chambres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chambres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chambres[]    findAll()
 * @method Chambres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChambresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambres::class);
    }

//    /**
//     * @return Chambres[] Returns an array of Chambres objects
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

//    public function findOneBySomeField($value): ?Chambres
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
