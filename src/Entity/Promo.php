<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PromoRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PromoRepository::class)]
class Promo
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $promoid = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Code promo should not be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Code promo cannot be longer than {{ limit }} characters.'
    )]
    private ?string $codepromo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'Date expiration should not be null.')]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'Date expiration must be today or in the future.'
    )]
    private ?\DateTimeInterface $dateexpiration = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Pourcentage promo should not be null.')]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: 'Pourcentage promo must be between {{ min }} and {{ max }}.'
    )]
    private ?int $pourcentagepromo = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Nombre max personne should not be null.')]
    #[Assert\Positive(message: 'Nombre max personne must be a positive number.')]
    private ?int $nombremaxpersonne = null;

    public function getPromoid(): ?int
    {
        return $this->promoid;
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
