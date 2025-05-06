<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationOffresVoyageRepository;
<<<<<<< HEAD
=======
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/master

#[ORM\Table(name: 'reservation_offres_voyage')]
#[ORM\Index(name: 'fk_client', columns: ['client_id'])]
#[ORM\Index(name: 'fk_offre_id', columns: ['offre_id'])]
#[ORM\Entity(repositoryClass: ReservationOffresVoyageRepository::class)]
class ReservationOffresVoyage
{
    #[ORM\Column(name: 'reservation_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $reservationId = null;

    #[ORM\Column(name: 'client_id')]
<<<<<<< HEAD
    private ?int $clientId = null;

    #[ORM\Column(name: 'offre_id')]
=======
    #[Assert\Positive(message: 'Client ID must be a positive integer.')]
    private ?int $clientId = null;

    #[ORM\Column(name: 'offre_id')]
    #[Assert\Positive(message: 'Offer ID must be a positive integer.')]
>>>>>>> origin/master
    private ?int $offreId = null;

    #[ORM\Column(name: 'date_reserved', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReserved = null;

    #[ORM\Column(name: 'status')]
    private ?int $status = null;

    #[ORM\Column(name: 'nbr_place')]
<<<<<<< HEAD
    private ?int $nbrPlace = null;

    #[ORM\Column(name: 'prix')]
=======
    #[Assert\Positive(message: 'Number of places must be positive.')]
    private ?int $nbrPlace = null;

    #[ORM\Column(name: 'prix')]
    #[Assert\GreaterThanOrEqual(0, message: 'Price cannot be negative.')]
>>>>>>> origin/master
    private ?float $prix = null;

    public function getReservationId(): ?int
    {
        return $this->reservationId;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getOffreId(): ?int
    {
        return $this->offreId;
    }

    public function setOffreId(int $offreId): static
    {
        $this->offreId = $offreId;

        return $this;
    }

    public function getDateReserved(): ?\DateTimeInterface
    {
        return $this->dateReserved;
    }

    public function setDateReserved(
        \DateTimeInterface $dateReserved,
    ): static {
        $this->dateReserved = \DateTime::createFromFormat('Y-m-d', $dateReserved->format('Y-m-d'));

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNbrPlace(): ?int
    {
        return $this->nbrPlace;
    }

    public function setNbrPlace(int $nbrPlace): static
    {
        $this->nbrPlace = $nbrPlace;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }
}
