<?php

namespace App\Repository;

use App\Entity\FlaggedContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FlaggedContent>
 *
 * @method FlaggedContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlaggedContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlaggedContent[]    findAll()
 * @method FlaggedContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlaggedContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlaggedContent::class);
    }

//    /**
//     * @return FlaggedContent[] Returns an array of FlaggedContent objects
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

//    public function findOneBySomeField($value): ?FlaggedContent
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
