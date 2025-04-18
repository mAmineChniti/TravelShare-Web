<?php

namespace App\Entity;

use App\Repository\OffresVoyageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Table(name: 'offres_voyage')]
#[ORM\Entity(repositoryClass: OffresVoyageRepository::class)]
class OffresVoyage
{
    #[ORM\Column(name: "offres_voyage_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $offresVoyageId = null;

    #[ORM\Column(name: "titre", length: 255)]
    private ?string $titre = null;

    #[ORM\Column(name: "destination", length: 255)]
    private ?string $destination = null;

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "date_depart", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(name: "date_retour", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateRetour = null;

    #[ORM\Column(name: "prix", type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $prix = null;

    #[ORM\Column(name: "places_disponibles")]
    private ?int $placesDisponibles = null;

    public function getOffresVoyageId(): ?int
    {
        return $this->offresVoyageId;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        if (empty(trim($titre))) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
        
        if (strlen($titre) > 255) {
            throw new InvalidArgumentException('Title cannot exceed 255 characters');
        }
        
        $this->titre = $titre;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        if (empty(trim($destination))) {
            throw new InvalidArgumentException('Destination cannot be empty');
        }
        
        if (strlen($destination) > 255) {
            throw new InvalidArgumentException('Destination cannot exceed 255 characters');
        }
        
        $this->destination = $destination;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        if($description === null) {
            throw new InvalidArgumentException('Description cannot be null');
        }
        if (empty(trim($description))) {
            throw new InvalidArgumentException('Description cannot be empty');
        }
        if (strlen($description) > 255) {
            throw new InvalidArgumentException('Description cannot exceed 255 characters');
        }
        if (strlen($description) < 10) {
            throw new InvalidArgumentException('Description must be at least 10 characters long');
        }
        $this->description = $description;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $today = new \DateTime('today');
        
        if ($dateDepart < $today) {
            throw new InvalidArgumentException('Departure date cannot be in the past');
        }
        
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeInterface
    {
        return $this->dateRetour;
    }

    public function setDateRetour(\DateTimeInterface $dateRetour): static
    {
        if ($this->dateDepart !== null && $dateRetour < $this->dateDepart) {
            throw new InvalidArgumentException('Return date cannot be before departure date');
        }
        
        $this->dateRetour = $dateRetour;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        if (!is_numeric($prix) || (float)$prix < 0) {
            throw new InvalidArgumentException('Price must be a non-negative number');
        }
        
        $this->prix = $prix;

        return $this;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->placesDisponibles;
    }

    public function setPlacesDisponibles(int $placesDisponibles): static
    {
        if ($placesDisponibles < 0) {
            throw new InvalidArgumentException('Available places cannot be negative');
        }
        
        $this->placesDisponibles = $placesDisponibles;

        return $this;
    }
}
