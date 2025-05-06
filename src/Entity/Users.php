<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $userId = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "First name is required")]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Last name is required")]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 50)]
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "Please enter a valid email address")]
    #[Assert\Length(max: 50, maxMessage: "Email cannot exceed {{ limit }} characters")]
    private ?string $email = null;

    #[ORM\Column(name: "password", length: 255)]
    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Length(min: 8, max: 255, minMessage: "Password must be at least {{ limit }} characters", maxMessage: "Password cannot exceed {{ limit }} characters")]
    #[Assert\Regex(pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", message: "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character")]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: "Phone number is required")]
    #[Assert\Length(min: 8, max: 15)]
    private ?string $phoneNum = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "Address is required")]
    #[Assert\Length(min: 5, max: 150)]
    private ?string $address = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $role = 0;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    #[Assert\Image(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"]
    )]
    private $photo = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $compte = 0;

    public function getRoles(): array
    {
        return 1 === $this->role ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // Effacer toute donnée sensible
    }

    public function getUserId(): ?int
    {
        return $this->userId;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoneNum(): ?int
    {
        return $this->phoneNum;
    }

    public function setPhoneNum(int $phoneNum): static
    {
        $this->phoneNum = $phoneNum;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(?int $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCompte(): ?int
    {
        return $this->compte;
    }

    public function setCompte(?int $compte): static
    {
        $this->compte = $compte;

        return $this;
    }
}
