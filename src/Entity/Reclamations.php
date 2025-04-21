<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReclamationsRepository;

#[ORM\Table(name: 'reclamations')]
#[ORM\Index(name: 'fk_user_id', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: ReclamationsRepository::class)]
class Reclamations
{
    #[ORM\Column(name: 'reclamation_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $reclamationId = null;

    #[ORM\Column(name: 'user_id')]
    private ?int $userId = null;

    #[ORM\Column(name: 'title', length: 50)]
    private ?string $title = null;

    #[ORM\Column(name: 'description', length: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_reclamation', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReclamation = null;

    #[ORM\Column(name: 'etat', length: 20, nullable: true, options: ['default' => 'en cours'])]
    private ?string $etat = 'en cours';

    public function getReclamationId(): ?int
    {
        return $this->reclamationId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

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

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(\DateTimeInterface $dateReclamation): static
    {
        $this->dateReclamation = $dateReclamation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
