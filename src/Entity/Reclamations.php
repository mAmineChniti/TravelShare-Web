<?php

namespace App\Entity;

use App\Repository\ReclamationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'reclamations')]
#[ORM\Index(name: 'fk_user_id', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: ReclamationsRepository::class)]
class Reclamations
{
    #[ORM\Column(name: "reclamation_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $reclamationId = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: false, onDelete: "CASCADE")]
    private ?Users $user = null;

    #[ORM\Column(name: "title", length: 100)]
    #[Assert\NotBlank(message: 'Please enter a subject for your complaint')]
    #[Assert\Length(
        min: 5,
        minMessage: 'Subject must be at least {{ limit }} characters',
        max: 100,
        maxMessage: 'Subject cannot be longer than {{ limit }} characters'
    )]
    private ?string $title = null;

    #[ORM\Column(name: "description", length: 255)]
    #[Assert\NotBlank(message: 'Please enter a description')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Description must be at least {{ limit }} characters',
        max: 2000,
        maxMessage: 'Description cannot be longer than {{ limit }} characters'
    )]
    private ?string $description = null;

    #[ORM\Column(name: "date_reclamation", type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Please select a date')]
    private ?\DateTimeInterface $dateReclamation = null;

    #[ORM\Column(name: "etat", length: 20, nullable: true, options: ["default" => 'en cours'])]
    private ?string $etat = 'en cours';

    #[ORM\OneToMany(mappedBy: 'reclamation', targetEntity: Reponses::class, cascade: ['persist', 'remove'])]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getReclamationId(): ?int
    {
        return $this->reclamationId;
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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Reponses>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponses $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setReclamation($this);
        }

        return $this;
    }

    public function removeReponse(Reponses $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
        }

        return $this;
    }
}

