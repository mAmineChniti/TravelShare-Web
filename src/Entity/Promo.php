<?php

namespace App\Entity;

use App\Repository\PromoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoRepository::class)]
class Promo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $promoid = null;

    #[ORM\Column(length: 255)]
    private ?string $codepromo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateexpiration = null;

    #[ORM\Column]
    private ?int $pourcentagepromo = null;

    #[ORM\Column]
    private ?int $nombremaxpersonne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPromoid(): ?int
    {
        return $this->promoid;
    }

    public function setPromoid(int $promoid): static
    {
        $this->promoid = $promoid;

        return $this;
    }

    public function getCodepromo(): ?string
    {
        return $this->codepromo;
    }

    public function setCodepromo(string $codepromo): static
    {
        $this->codepromo = $codepromo;

        return $this;
    }

    public function getDateexpiration(): ?\DateTimeInterface
    {
        return $this->dateexpiration;
    }

    public function setDateexpiration(\DateTimeInterface $dateexpiration): static
    {
        $this->dateexpiration = $dateexpiration;

        return $this;
    }

    public function getPourcentagepromo(): ?int
    {
        return $this->pourcentagepromo;
    }

    public function setPourcentagepromo(int $pourcentagepromo): static
    {
        $this->pourcentagepromo = $pourcentagepromo;

        return $this;
    }

    public function getNombremaxpersonne(): ?int
    {
        return $this->nombremaxpersonne;
    }

    public function setNombremaxpersonne(int $nombremaxpersonne): static
    {
        $this->nombremaxpersonne = $nombremaxpersonne;

        return $this;
    }
}
