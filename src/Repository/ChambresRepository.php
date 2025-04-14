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

    /**
     * Retourne les chambres disponibles pour un hôtel spécifique
     *
     * @param int $hotelId
     * @return Chambres[]
     */
    public function findByHotelId(int $hotelId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.hotel = :hotelId')
            ->setParameter('hotelId', $hotelId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les chambres *disponibles* pour un hôtel
     */
    public function findAvailableByHotelId(int $hotelId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.hotel = :hotelId')
            ->andWhere('c.disponible = true')
            ->setParameter('hotelId', $hotelId)
            ->getQuery()
            ->getResult();
    }
}
