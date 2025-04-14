<?php

namespace App\Entity;

use App\Repository\ChambresRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'chambres')]
#[ORM\Index(name: 'fk_hotel_id', columns: ['hotel_id'])]
#[ORM\Entity(repositoryClass: ChambresRepository::class)]
class Chambres
{
    #[ORM\Column(name: "chambre_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $chambreId = null;

    #[ORM\ManyToOne(targetEntity: Hotels::class)]
    #[ORM\JoinColumn(name: "hotel_id", referencedColumnName: "hotel_id", nullable: false)]
    private ?Hotels $hotel = null;

    #[ORM\Column(name: "numero_chambre", length: 255)]
    private ?string $numeroChambre = null;

    #[ORM\Column(name: "type_enu", type: Types::STRING)]
    private ?string $typeEnu = null;

    #[ORM\Column(name: "prix_par_nuit", type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $prixParNuit = null;

    #[ORM\Column(name: "disponible")]
    private ?int $disponible = null;

    // Getters and Setters

    public function getChambreId(): ?int
    {
        return $this->chambreId;
    }

    public function getHotel(): ?Hotels
    {
        return $this->hotel;
    }

    public function setHotel(?Hotels $hotel): static
    {
        $this->hotel = $hotel;
        return $this;
    }

    public function getNumeroChambre(): ?string
    {
        return $this->numeroChambre;
    }

    public function setNumeroChambre(string $numeroChambre): static
    {
        $this->numeroChambre = $numeroChambre;
        return $this;
    }

    public function getTypeEnu(): ?string
    {
        return $this->typeEnu;
    }

    public function setTypeEnu(string $typeEnu): static
    {
        $this->typeEnu = $typeEnu;
        return $this;
    }

    public function getPrixParNuit(): ?string
    {
        return $this->prixParNuit;
    }

    public function setPrixParNuit(string $prixParNuit): static
    {
        $this->prixParNuit = $prixParNuit;
        return $this;
    }

    public function getDisponible(): ?int
    {
        return $this->disponible;
    }

    public function setDisponible(int $disponible): static
    {
        $this->disponible = $disponible;
        return $this;
    }
}
