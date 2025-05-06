<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationPacksRepository;

#[ORM\Table(name: 'reservation_packs')]
#[ORM\Index(name: 'fk_packs_client_id', columns: ['client_id'])]
#[ORM\Index(name: 'fk_pack_id', columns: ['pack_id'])]
#[ORM\Entity(repositoryClass: ReservationPacksRepository::class)]
class ReservationPacks
{
    #[ORM\Column(name: 'reservation_packs_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $reservationPacksId = null;

    #[ORM\Column(name: 'client_id')]
    private ?int $clientId = null;

    #[ORM\Column(name: 'pack_id')]
    private ?int $packId = null;

    #[ORM\Column(name: 'date_reservation', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;

    #[ORM\Column(name: 'statut', type: Types::STRING)]
    private ?string $statut = null;

    #[ORM\Column(name: 'prix_total', type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $prixTotal = null;

    public function getReservationPacksId(): ?int
    {
        return $this->reservationPacksId;
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

    public function getPackId(): ?int
    {
        return $this->packId;
    }

    public function setPackId(int $packId): static
    {
        $this->packId = $packId;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPrixTotal(): ?string
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(string $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }
}
