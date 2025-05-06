<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GuidesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'guides')]
#[ORM\Entity(repositoryClass: GuidesRepository::class)]
class Guides
{
    #[ORM\OneToMany(mappedBy: 'guide', targetEntity: Excursions::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $excursions;

    public function __construct()
    {
        $this->excursions = new ArrayCollection();
    }

    #[ORM\Column(name: 'guide_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $guideId = null;

    #[ORM\Column(name: 'name', length: 50)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'Le prénom ne peut pas dépasser 50 caractères.')]
    private ?string $name = null;

    #[ORM\Column(name: 'last_name', length: 50)]
    #[Assert\NotBlank(message: 'Le nom de famille ne peut pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom de famille ne peut pas dépasser 50 caractères.')]
    private ?string $lastName = null;

    #[ORM\Column(name: 'email', length: 50)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    #[Assert\Length(max: 50, maxMessage: "L'email ne peut pas dépasser 50 caractères.")]
    private ?string $email = null;

    #[ORM\Column(name: 'phone_num', length: 50)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone ne peut pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'Le numéro de téléphone ne peut pas dépasser 50 caractères.')]
    private ?string $phoneNum = null;

    #[ORM\Column(name: 'language', length: 50)]
    #[Assert\NotBlank(message: 'La langue ne peut pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'La langue ne peut pas dépasser 50 caractères.')]
    private ?string $language = null;

    #[ORM\Column(name: 'experience')]
    #[Assert\NotBlank(message: "L'expérience ne peut pas être vide.")]
    #[Assert\GreaterThanOrEqual(0, message: "L'expérience doit être un nombre positif.")]
    private ?int $experience = null;

    public function getGuideId(): ?int
    {
        return $this->guideId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNum(): ?string
    {
        return $this->phoneNum;
    }

    public function setPhoneNum(string $phoneNum): static
    {
        $this->phoneNum = $phoneNum;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): static
    {
        $this->experience = $experience;

        return $this;
    }

    public function getExcursions(): Collection
    {
        return $this->excursions;
    }

    public function addExcursion(Excursions $excursion): self
    {
        if (!$this->excursions->contains($excursion)) {
            $this->excursions->add($excursion);
            $excursion->setGuide($this);
        }

        return $this;
    }

    public function removeExcursion(Excursions $excursion): self
    {
        if ($this->excursions->removeElement($excursion)) {
            // set the owning side to null (unless already changed)
            if ($excursion->getGuide() === $this) {
                $excursion->setGuide(null);
            }
        }

        return $this;
    }
}
