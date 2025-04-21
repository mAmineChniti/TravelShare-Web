<?php

namespace App\Repository;

use App\Entity\ReservationHotel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function add(ReservationHotel $reservationHotel): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($reservationHotel);
        $entityManager->flush();
    }

    public function update(ReservationHotel $reservationHotel): void
    {
        $entityManager = $this->getEntityManager();
        $existingReservation = $this->find($reservationHotel->getReservationHotelId());
        if (!$existingReservation) {
            throw new \Exception('Reservation not found');
        }
        $existingReservation->setClientId($reservationHotel->getClientId());
        $existingReservation->setChambreId($reservationHotel->getChambreId());
        $existingReservation->setDateDebut($reservationHotel->getDateDebut());
        $existingReservation->setDateFin($reservationHotel->getDateFin());
        $existingReservation->setStatusEnu($reservationHotel->getStatusEnu());
        $existingReservation->setPrixTotale($reservationHotel->getPrixTotale());
        $entityManager->flush();
    }

    public function delete(int $id): void
    {
        $entityManager = $this->getEntityManager();
        $reservation = $entityManager->getReference(ReservationHotel::class, $id);
        $entityManager->remove($reservation);
        $entityManager->flush();
    }

    public function listAll(): array
    {
        return $this->findAll();
    }
}
