<?php

namespace App\Repository;

use App\Entity\ReservationHotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationHotel>
 *
 * @method ReservationHotel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationHotel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationHotel[]    findAll()
 * @method ReservationHotel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationHotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationHotel::class);
    }

    public function add(ReservationHotel $reservation, bool $flush = true): void
    {
        $this->_em->persist($reservation);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function update(ReservationHotel $reservation, bool $flush = true): void
    {
        // La méthode update peut simplement persister à nouveau et flush,
        // car Doctrine se charge de synchroniser les changements.
        $this->_em->persist($reservation);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(ReservationHotel $reservation, bool $flush = true): void
    {
        $this->_em->remove($reservation);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Retourne toutes les réservations.
     *
     * @return ReservationHotel[]
     */
    public function listAll(): array
    {
        return $this->findAll();
    }
}
