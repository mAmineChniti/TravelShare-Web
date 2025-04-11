<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(name: "user_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $userId = null;

    #[ORM\Column(name: "name", length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères", maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $name = null;

    #[ORM\Column(name: "last_name", length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le nom doit contenir au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 50)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(max: 50, maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères")]
    private ?string $email = null;

    #[ORM\Column(name: "password", length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire", groups: ["registration"])]
    #[Assert\Length(min: 8, max: 255, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères", maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères", groups: ["registration"])]
    #[Assert\Regex(pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", message: "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial", groups: ["registration"])]
    private ?string $password = null;

    #[ORM\Column(name: "phone_num")]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire")]
    #[Assert\Length(min: 8, max: 15, minMessage: "Le numéro de téléphone doit contenir au moins {{ limit }} chiffres", maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres")]
    private ?string $phoneNum = null;

    #[ORM\Column(name: "address", length: 150)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(min: 5, max: 150, minMessage: "L'adresse doit contenir au moins {{ limit }} caractères", maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères")]
    private ?string $address = null;

    #[ORM\Column(name: "role", nullable: true, options: ["default" => 0])]
    private ?int $role = 0;

    #[ORM\Column(name: "photo", type: Types::BLOB, nullable: true)]
    #[Assert\Image(maxSize: "2M", mimeTypes: ["image/jpeg", "image/png", "image/gif"], mimeTypesMessage: "Veuillez uploader une image valide (JPEG, PNG ou GIF)")]
    private $photo = null;

    #[ORM\Column(name: "compte", nullable: true, options: ["default" => 0])]
    private ?int $compte = 0;

    public function getRoles(): array
    {
        return $this->role === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER'];
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
