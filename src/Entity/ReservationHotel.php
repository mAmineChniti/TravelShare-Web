<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationHotelRepository;
<<<<<<< HEAD
=======
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/master

#[ORM\Table(name: 'reservation_hotel')]
#[ORM\Index(name: 'fk_client_id', columns: ['client_id'])]
#[ORM\Index(name: 'fk_chambre_id', columns: ['chambre_id'])]
#[ORM\Entity(repositoryClass: ReservationHotelRepository::class)]
class ReservationHotel
{
    #[ORM\Column(name: 'reservation_hotel_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $reservationHotelId = null;

    #[ORM\Column(name: 'client_id')]
    private ?int $clientId = null;

    #[ORM\Column(name: 'chambre_id')]
    private ?int $chambreId = null;

    #[ORM\Column(name: 'date_debut', type: Types::DATE_MUTABLE)]
<<<<<<< HEAD
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'status_enu', type: Types::STRING)]
    private ?string $statusEnu = null;

    #[ORM\Column(name: 'prix_totale')]
=======
    #[Assert\NotBlank(message: 'Start date cannot be blank.')]
    #[Assert\GreaterThanOrEqual('today', message: 'Start date cannot be in the past.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'End date cannot be blank.')]
    #[Assert\Expression(
        'this.getDateDebut() === null || value >= this.getDateDebut()',
        message: 'End date cannot be before start date.'
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'status_enu', type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Status cannot be blank.')]
    #[Assert\Choice(choices: ['confirmer', 'annuler', 'en attente', ''], message: 'Invalid status. Allowed values are: confirmer, annuler, en attente, or empty.')]
    private ?string $statusEnu = null;

    #[ORM\Column(name: 'prix_totale')]
    #[Assert\Positive(message: 'Total price must be a positive number.')]
>>>>>>> origin/master
    private ?int $prixTotale = null;

    public function getReservationHotelId(): ?int
    {
        return $this->reservationHotelId;
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

    public function getChambreId(): ?int
    {
        return $this->chambreId;
    }

    public function setChambreId(int $chambreId): static
    {
        $this->chambreId = $chambreId;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getStatusEnu(): ?string
    {
        return $this->statusEnu;
    }

    public function setStatusEnu(string $statusEnu): static
    {
        $this->statusEnu = $statusEnu;

        return $this;
    }

    public function getPrixTotale(): ?int
    {
        return $this->prixTotale;
    }

    public function setPrixTotale(int $prixTotale): static
    {
        $this->prixTotale = $prixTotale;

        return $this;
    }
}
