<?php

namespace App\Entity;

use App\Repository\ExcursionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'excursions')]
#[ORM\Index(name: 'fk_id_guide', columns: ['guide_id'])]
#[ORM\Entity(repositoryClass: ExcursionsRepository::class)]
class Excursions
{
    #[ORM\Column(name: "excursion_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $excursionId = null;

    #[ORM\Column(name: "guide_id")]
    private ?int $guideId = null;

    #[ORM\Column(name: "title", length: 50)]
    #[Assert\NotBlank(message: "Le titre de l'excursion est requis.")]
    #[Assert\Length(max: 50, maxMessage: "Le titre ne peut pas dépasser 50 caractères.")]
    private ?string $title = null;

    #[ORM\Column(name: "description", length: 255)]
    #[Assert\NotBlank(message: "La description de l'excursion est requise.")]
    #[Assert\Length(max: 255, maxMessage: "La description ne peut pas dépasser 255 caractères.")]
    private ?string $description = null;

    #[ORM\Column(name: "date_excursion", type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de l'excursion est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être au format valide.")]
    #[Assert\GreaterThan("today", message: "La date de l'excursion doit être dans le futur.")]
    private ?\DateTimeInterface $dateExcursion = null;

    #[ORM\Column(name: "date_fin", type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin de l'excursion est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "La date de fin doit être au format valide.")]
    #[Assert\GreaterThan(propertyPath: "dateExcursion", message: "La date de fin doit être après la date de l'excursion.")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: "image", type: Types::BLOB)]
    #[Assert\NotBlank(message: "L'image est requise.")]
    private $image = null;

    #[ORM\Column(name: "prix")]
    #[Assert\NotBlank(message: "Le prix est requis.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $prix = null;

    public function getExcursionId(): ?int
    {
        return $this->excursionId;
    }

    public function getGuideId(): ?int
    {
        return $this->guideId;
    }

    public function setGuideId(int $guideId): static
    {
        $this->guideId = $guideId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateExcursion(): ?\DateTimeInterface
    {
        return $this->dateExcursion;
    }

    public function setDateExcursion(\DateTimeInterface $dateExcursion): static
    {
        $this->dateExcursion = $dateExcursion;

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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): static
    {
        $this->image = $image;

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
