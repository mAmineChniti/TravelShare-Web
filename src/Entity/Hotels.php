<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HotelsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'hotels')]
#[ORM\Entity(repositoryClass: HotelsRepository::class)]
class Hotels
{
    #[ORM\Column(name: 'hotel_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $hotelId = null;

    #[ORM\Column(name: 'nom', length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty.')]
    #[Assert\Length(max: 255, maxMessage: 'The name cannot exceed 255 characters.')]
    private ?string $nom = null;

    #[ORM\Column(name: 'adress', type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'The address cannot be empty.')]
    #[Assert\Length(max: 255, maxMessage: 'The address cannot exceed 255 characters.')]
    private ?string $adress = null;

    #[ORM\Column(name: 'telephone', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'The telephone number cannot be empty.')]
    #[Assert\Length(min: 8, max: 8, exactMessage: 'The telephone number must be exactly 8 digits.')]
    #[Assert\Regex(pattern: '/^[0-9]+$/', message: 'Invalid phone number format. Only numbers are allowed.')]
    private ?string $telephone = null;

    #[ORM\Column(name: 'capacite_totale')]
    #[Assert\Positive(message: 'The total capacity must be more than 0.')]
    private ?int $capaciteTotale = null;

    #[ORM\Column(name: 'image_h', type: Types::BLOB)]
    private $imageH;

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
