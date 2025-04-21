<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Column(name: 'user_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $userId = null;

    #[ORM\Column(name: 'name', length: 50)]
    private ?string $name = null;

    #[ORM\Column(name: 'last_name', length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(name: 'email', length: 50)]
    private ?string $email = null;

    #[ORM\Column(name: 'password', length: 255)]
    private ?string $password = null;

    #[ORM\Column(name: 'phone_num')]
    private ?int $phoneNum = null;

    #[ORM\Column(name: 'address', length: 150)]
    private ?string $address = null;

    #[ORM\Column(name: 'role', nullable: true, options: ['default' => 0])]
    private ?int $role = 0;

    #[ORM\Column(name: 'photo', type: Types::BLOB, nullable: true)]
    private $photo;

    #[ORM\Column(name: 'compte', nullable: true, options: ['default' => 0])]
    private ?int $compte = 0;

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
