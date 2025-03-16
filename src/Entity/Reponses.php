<?php

namespace App\Entity;

use App\Repository\ReponsesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'reponses')]
#[ORM\Index(name: 'fk_reclamation_id', columns: ['reclamation_id'])]
#[ORM\Entity(repositoryClass: ReponsesRepository::class)]
class Reponses
{
    #[ORM\Column(name: "reponse_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $reponseId = null;

    #[ORM\Column(name: "reclamation_id")]
    private ?int $reclamationId = null;

    #[ORM\Column(name: "contenu", length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(name: "date_reponse", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReponse = null;

    public function getReponseId(): ?int
    {
        return $this->reponseId;
    }

    public function getReclamationId(): ?int
    {
        return $this->reclamationId;
    }

    public function setReclamationId(int $reclamationId): static
    {
        $this->reclamationId = $reclamationId;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(\DateTimeInterface $dateReponse): static
    {
        $this->dateReponse = $dateReponse;

        return $this;
    }
}
