<?php

namespace App\Entity;

use App\Repository\HotelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Chambres;

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

    #[ORM\OneToMany(mappedBy: 'hotel', targetEntity: Chambres::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $chambres;

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->hotelId;
    }

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
        if (empty($nom)) {
            throw new \InvalidArgumentException('The name cannot be empty.');
        }

        if (strlen($nom) > 255) {
            throw new \InvalidArgumentException('The name cannot exceed 255 characters.');
        }

        $this->nom = $nom;
        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        if ($adress !== null && strlen($adress) > 255) {
            throw new \InvalidArgumentException('The address cannot exceed 255 characters.');
        }
        if(empty($adress)){
            throw new \InvalidArgumentException('The address cannot be empty.');
        }

        $this->adress = $adress;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        if ($telephone === '') {
            throw new \InvalidArgumentException('The telephone number cannot be empty.');
        }
        if ($telephone !== null) {
            if (strlen($telephone) > 255) {
                throw new \InvalidArgumentException('The telephone number cannot exceed 255 characters.');
            }

            if (!preg_match('/^[0-9]+$/', $telephone)) {
                throw new \InvalidArgumentException('Invalid phone number format. Only numbers are allowed.');
            }
        }

        $this->telephone = $telephone;
        return $this;
    }

    public function getCapaciteTotale(): ?int
    {
        return $this->capaciteTotale;
    }

    public function setCapaciteTotale(int $capaciteTotale): static
    {
        if ($capaciteTotale <= 0) {
            throw new \InvalidArgumentException('The total capacity must be more than 0.');
        }

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

    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambres $chambre): static
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres[] = $chambre;
            $chambre->setHotel($this);
        }

        return $this;
    }

    public function removeChambre(Chambres $chambre): static
    {
        if ($this->chambres->removeElement($chambre)) {
            if ($chambre->getHotel() === $this) {
                $chambre->setHotel(null);
            }
        }

        return $this;
    }
}
