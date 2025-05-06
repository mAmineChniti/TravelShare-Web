<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OffresVoyageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'offres_voyage')]
#[ORM\Entity(repositoryClass: OffresVoyageRepository::class)]
class OffresVoyage
{
    #[ORM\Column(name: 'offres_voyage_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $offresVoyageId = null;

    #[ORM\Column(name: 'titre', length: 255)]
    #[Assert\NotBlank(message: 'Title cannot be empty.')]
    #[Assert\Length(max: 255, maxMessage: 'Title cannot exceed 255 characters.')]
    private ?string $titre = null;

    #[ORM\Column(name: 'destination', length: 255)]
    #[Assert\NotBlank(message: 'Destination cannot be empty.')]
    #[Assert\Length(max: 255, maxMessage: 'Destination cannot exceed 255 characters.')]
    private ?string $destination = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'Description cannot be empty.')]
    #[Assert\Length(min: 10, max: 255, minMessage: 'Description must be at least 10 characters long.', maxMessage: 'Description cannot exceed 255 characters.')]
    private ?string $description = null;

    #[ORM\Column(name: 'date_depart', type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThanOrEqual('today', message: 'Departure date cannot be in the past.')]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(name: 'date_retour', type: Types::DATE_MUTABLE)]
    #[Assert\Expression(
        'this.getDateDepart() === null || value >= this.getDateDepart()',
        message: 'Return date cannot be before departure date.'
    )]
    private ?\DateTimeInterface $dateRetour = null;

    #[ORM\Column(name: 'prix', type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\GreaterThanOrEqual(0, message: 'Price must be a non-negative number.')]
    private ?string $prix = null;

    #[ORM\Column(name: 'places_disponibles')]
    #[Assert\GreaterThanOrEqual(0, message: 'Available places cannot be negative.')]
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
        $this->titre = $titre;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeInterface
    {
        return $this->dateRetour;
    }

    public function setDateRetour(\DateTimeInterface $dateRetour): static
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->placesDisponibles;
    }

    public function setPlacesDisponibles(int $placesDisponibles): static
    {
        $this->placesDisponibles = $placesDisponibles;

        return $this;
    }
}
