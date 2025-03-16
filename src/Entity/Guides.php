<?php

namespace App\Entity;

use App\Repository\GuidesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'guides')]
#[ORM\Entity(repositoryClass: GuidesRepository::class)]
class Guides
{
    #[ORM\Column(name: "guide_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $guideId = null;

    #[ORM\Column(name: "name", length: 50)]
    private ?string $name = null;

    #[ORM\Column(name: "last_name", length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 50)]
    private ?string $email = null;

    #[ORM\Column(name: "phone_num", length: 50)]
    private ?string $phoneNum = null;

    #[ORM\Column(name: "language", length: 50)]
    private ?string $language = null;

    #[ORM\Column(name: "experience")]
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
}
