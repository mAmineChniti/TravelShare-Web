<?php

namespace App\Entity;

use App\Repository\HotelsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hotels')]
#[ORM\Entity(repositoryClass: HotelsRepository::class)]
class Hotels
{
    #[ORM\Column(name: "hotel_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $hotelId = null;

    #[ORM\Column(name: "nom", length: 255)]
    private ?string $nom = null;

    #[ORM\Column(name: "adress", type: Types::TEXT, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(name: "telephone", length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "capacite_totale")]
    private ?int $capaciteTotale = null;

    #[ORM\Column(name: "image_h", type: Types::BLOB)]
    private $imageH = null;

    public function getHotelId(): ?int
    {
        return $this->hotelId;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCapaciteTotale(): ?int
    {
        return $this->capaciteTotale;
    }

    public function setCapaciteTotale(int $capaciteTotale): static
    {
        $this->capaciteTotale = $capaciteTotale;

        return $this;
    }

    public function getImageH()
    {
        return $this->imageH;
    }

    public function setImageH($imageH): static
    {
        $this->imageH = $imageH;

        return $this;
    }
}
