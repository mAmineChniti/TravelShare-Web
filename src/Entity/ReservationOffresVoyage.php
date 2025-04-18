<?php

namespace App\Entity;

use App\Repository\ReservationOffresVoyageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Table(name: 'reservation_offres_voyage')]
#[ORM\Index(name: 'fk_client', columns: ['client_id'])]
#[ORM\Index(name: 'fk_offre_id', columns: ['offre_id'])]
#[ORM\Entity(repositoryClass: ReservationOffresVoyageRepository::class)]
class ReservationOffresVoyage
{
        #[ORM\Column(name: "reservation_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $reservationId = null;

    #[ORM\Column(name: "client_id")]
    private ?int $clientId = null;

    #[ORM\Column(name: "offre_id")]
    private ?int $offreId = null;

    #[ORM\Column(name: "date_reserved", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReserved = null;

    #[ORM\Column(name: "status")]
    private ?int $status = null;

    #[ORM\Column(name: "nbr_place")]
    private ?int $nbrPlace = null;

    #[ORM\Column(name: "prix")]
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
        if ($clientId <= 0) {
            throw new InvalidArgumentException('Client ID must be a positive integer');
        }
        $this->clientId = $clientId;

        return $this;
    }

    public function getOffreId(): ?int
    {
        return $this->offreId;
    }

    public function setOffreId(int $offreId): static
    {
        if ($offreId <= 0) {
            throw new InvalidArgumentException('Offer ID must be a positive integer');
        }
        $this->offreId = $offreId;

        return $this;
    }

    public function getDateReserved(): ?\DateTimeInterface
    {
        return $this->dateReserved;
    }

    public function setDateReserved(\DateTimeInterface $dateReserved): static
    {
        $currentDate = new \DateTime();
        if ($dateReserved > $currentDate) {
            throw new InvalidArgumentException('Reservation date cannot be in the future');
        }
        $this->dateReserved = $dateReserved;

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
        if ($nbrPlace <= 0) {
            throw new InvalidArgumentException('Number of places must be positive');
        }
        $this->nbrPlace = $nbrPlace;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        if ($prix < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
        $this->prix = $prix;

        return $this;
    }
}
