<?php

namespace App\Repository;

use App\Entity\Promo;
use App\Entity\OffresVoyage;
use App\Entity\ReservationOffresVoyage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ReservationOffresVoyageRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, ReservationOffresVoyage::class);
        $this->entityManager = $entityManager;
    }

    public function add(ReservationOffresVoyage $reservation, string $promoCode): void
    {
        $offre = $this->entityManager->getRepository(OffresVoyage::class)->find($reservation->getOffreId());

        if ($offre->getPlacesDisponibles() < $reservation->getNbrPlace()) {
            throw new \Exception('Not enough available places for this reservation.');
        }

        if ($promoCode) {
            $promo = $this->entityManager->getRepository(Promo::class)->findOneBy(['codepromo' => $promoCode]);
            if ($promo) {
                $pourcentagePromo = $promo->getPourcentagePromo();
                if ($pourcentagePromo < 1 || $pourcentagePromo > 100) {
                    throw new \Exception('Promo percentage must be between 1 and 100.');
                }
                $totalPrix = $offre->getPrix() * $reservation->getNbrPlace() * (1 - $pourcentagePromo / 100);
            } else {
                throw new \Exception('Invalid promo code.');
            }
        } else {
            $totalPrix = $offre->getPrix() * $reservation->getNbrPlace();
        }

        if ($totalPrix < 0) {
            throw new \Exception('Total price cannot be negative.');
        }

        $reservation->setPrix($totalPrix);
        $reservation->setDateReserved(new \DateTime());
        $reservation->setStatus(1); // Default status (e.g., 1 for pending)

        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() - $reservation->getNbrPlace());
        $this->entityManager->persist($reservation);
        $this->entityManager->persist($offre);
        $this->entityManager->flush();
    }

    public function update(ReservationOffresVoyage $reservation): void
    {
        $existingReservation = $this->find($reservation->getReservationId());
        if (!$existingReservation) {
            throw new \Exception('Reservation not found.');
        }
        $offre = $this->entityManager->getRepository(OffresVoyage::class)->find($reservation->getOffreId());

        $placesDifference = $reservation->getNbrPlace() - $existingReservation->getNbrPlace();

        if ($offre->getPlacesDisponibles() < $placesDifference) {
            throw new \Exception('Not enough available places for this reservation.');
        }

        $totalPrix = $offre->getPrix() * $reservation->getNbrPlace();
        $reservation->setPrix($totalPrix);

        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() - $placesDifference);

        $this->entityManager->flush();
    }

    public function delete(ReservationOffresVoyage $reservation): void
    {
        $offre = $this->entityManager->getRepository(OffresVoyage::class)->find($reservation->getOffreId());
        $offre->setPlacesDisponibles($offre->getPlacesDisponibles() + $reservation->getNbrPlace());

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
    }

    public function findAllReservations(): array
    {
        return $this->findAll();
    }
}
