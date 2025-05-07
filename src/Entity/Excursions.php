<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ExcursionsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'excursions')]
#[ORM\Entity(repositoryClass: ExcursionsRepository::class)]
class Excursions
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'excursion_id')]
    private ?int $excursionId = null;

    #[ORM\ManyToOne(targetEntity: Guides::class, inversedBy: 'excursions')]
    #[ORM\JoinColumn(name: 'guide_id', referencedColumnName: 'guide_id', onDelete: 'CASCADE')]
    private ?Guides $guide = null;

    #[ORM\Column(name: 'guide_id')]
    private ?int $guideId = null;

    #[ORM\Column(name: 'title', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $title = null;

    #[ORM\Column(name: 'description', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_excursion', type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateExcursion = null;

    #[ORM\Column(name: 'date_fin', type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'image', type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'prix')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?float $prix = null;

    #[ORM\OneToMany(targetEntity: ListeFavoris::class, mappedBy: 'excursion')]
    private Collection $favoris;

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
    }

    public function getExcursionId(): ?int
    {
        return $this->excursionId;
    }

    public function getGuide(): ?Guides
    {
        return $this->guide;
    }

    public function setGuide(?Guides $guide): static
    {
        $this->guide = $guide;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function isPast(): bool
    {
        $now = new \DateTime('today');

        return $this->dateExcursion < $now;
    }

    public function getFavoris(): Collection
    {
        return $this->favoris;
    }
}
