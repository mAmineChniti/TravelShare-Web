<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ExcursionsRepository;

#[ORM\Table(name: 'excursions')]
#[ORM\Index(name: 'fk_id_guide', columns: ['guide_id'])]
#[ORM\Entity(repositoryClass: ExcursionsRepository::class)]
class Excursions
{
    #[ORM\Column(name: 'excursion_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $excursionId = null;

    #[ORM\Column(name: 'guide_id')]
    private ?int $guideId = null;

    #[ORM\Column(name: 'title', length: 50)]
    private ?string $title = null;

    #[ORM\Column(name: 'description', length: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_excursion', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateExcursion = null;

    #[ORM\Column(name: 'date_fin', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'image', type: Types::BLOB)]
    private $image;

    #[ORM\Column(name: 'prix')]
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
