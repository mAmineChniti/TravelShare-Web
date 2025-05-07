<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ListeFavorisRepository;

#[ORM\Entity(repositoryClass: ListeFavorisRepository::class)]
class ListeFavoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'favoris')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false)]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: Excursions::class, inversedBy: 'favoris')]
    #[ORM\JoinColumn(name: 'excursion_id', referencedColumnName: 'excursion_id', nullable: false)]
    private ?Excursions $excursion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExcursion(): ?Excursions
    {
        return $this->excursion;
    }

    public function setExcursion(?Excursions $excursion): self
    {
        $this->excursion = $excursion;

        return $this;
    }
}
