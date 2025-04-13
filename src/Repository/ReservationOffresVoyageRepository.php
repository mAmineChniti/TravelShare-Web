<?php
namespace App\Service;

use App\Entity\ReservationOffresVoyage;
use App\Entity\OffresVoyage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\DBAL\Exception;

class ReservationOffresVoyageService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(ReservationOffresVoyage $reservation): void
    {
        $offre = $this->em->getRepository(OffresVoyage::class)->find($reservation->getOffre());

        if (!$offre) {
            throw new EntityNotFoundException('Offre not found');
        }

        if ($reservation->getNbrPlace() > $offre->getPlacesDisponibles()) {
            throw new \Exception('Not enough available places for this reservation.');
        }

        $totalPrix = $offre->getPrix() * $reservation->getNbrPlace();
        $reservation->setPrix($totalPrix);
        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() - $reservation->getNbrPlace());

        $this->em->persist($reservation);
        $this->em->flush();
    }

    public function update(ReservationOffresVoyage $reservation): void
    {
        $existingReservation = $this->em->getRepository(ReservationOffresVoyage::class)->find($reservation->getId());
        if (!$existingReservation) {
            throw new EntityNotFoundException('Reservation not found');
        }

        $offre = $this->em->getRepository(OffresVoyage::class)->find($reservation->getOffre());
        if (!$offre) {
            throw new EntityNotFoundException('Offre not found');
        }

        $oldNbrPlace = $existingReservation->getNbrPlace();
        $diff = $reservation->getNbrPlace() - $oldNbrPlace;

        if ($diff > 0 && $diff > $offre->getPlacesDisponibles()) {
            throw new \Exception('Not enough available places for update.');
        }

        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() - $diff);

        $reservation->setPrix($offre->getPrix() * $reservation->getNbrPlace());
        $this->em->merge($reservation);
        $this->em->flush();
    }

    public function delete(int $id): void
    {
        $reservation = $this->em->getRepository(ReservationOffresVoyage::class)->find($id);

        if (!$reservation) {
            throw new EntityNotFoundException('Reservation not found');
        }

        $offre = $reservation->getOffre();
        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() + $reservation->getNbrPlace());

        $this->em->remove($reservation);
        $this->em->flush();
    }

    /**
     * @return ReservationOffresVoyage[]
     */
    public function listAll(): array
    {
        return $this->em->getRepository(ReservationOffresVoyage::class)->findAll();
    }
}
