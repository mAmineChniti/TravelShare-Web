<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email is already in use.', entityClass: Users::class)]

class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $userId = null;

    #[ORM\Column(length: 50)]
    //#[Assert\NotBlank(message: "First name is required")]
        //#[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    //#[Assert\NotBlank(message: "Last name is required")]
        //#[Assert\Length(min: 2, max: 50)]
    private ?string $lastName = null;

    #[ORM\Column(name: "email", length: 50, unique: true)]
    //#[Assert\NotBlank(message: "Email is required")]
        //#[Assert\Email(message: "Please enter a valid email address")]
        //#[Assert\Length(max: 50, maxMessage: "Email cannot exceed {{ limit }} characters")]
    private ?string $email = null;

    #[ORM\Column(name: "password", length: 255)]
    //#[Assert\NotBlank(message: "Password is required")]
        //#[Assert\Length(min: 8, max: 255, minMessage: "Password must be at least {{ limit }} characters", maxMessage: "Password cannot exceed {{ limit }} characters")]
        //#[Assert\Regex(
        //pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
        //message: "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character"
        //)]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    //#[Assert\NotBlank(message: "Phone number is required")]
        //#[Assert\Length(min: 8, max: 15)]
    private ?string $phoneNum = null;

    #[ORM\Column(length: 150)]
    //#[Assert\NotBlank(message: "Address is required")]
        //#[Assert\Length(min: 5, max: 150)]
    private ?string $address = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $role = 0;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    //#[Assert\Image(maxSize: "2M", mimeTypes: ["image/jpeg", "image/png", "image/gif"])]
    private $photo = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $compte = 0;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this); // Assurez-vous que vous utilisez 'setUser' ici et non 'setUserId'.
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) { // Utilisez 'getUser' et non 'getUserId'
                $notification->setUser(null);
            }
        }

        return $this;
    }


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

    public function getPhoneNum(): ?string
    {
        return $this->phoneNum;
    }

    public function setPhoneNum(string $phoneNum): static
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

    //modification

    public function getPhoto(): ?string
    {
        // Si c'est une ressource (cas typique avec Doctrine et BLOB)
        if (is_resource($this->photo)) {
            rewind($this->photo); // Important : rembobine le pointeur
            return stream_get_contents($this->photo);
        }

        // Si c'est déjà une chaîne binaire
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        // Si c'est un upload Symfony File
        if ($photo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $this->photo = file_get_contents($photo->getPathname());
        }
        // Si c'est une chaîne ou une ressource
        else {
            $this->photo = $photo;
        }

        return $this;
    }

    // Nouvelle méthode pour l'affichage en base64
    public function getPhotoBase64(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        $photoData = $this->getPhoto();
        return 'data:image/jpeg;base64,' . base64_encode($photoData);
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

    public function isBlocked(): bool
    {
        return $this->compte === 1; // 1 = bloqué, 0 = actif
    }

    public function block(): void
    {
        $this->compte = 1; // Bloquer l'utilisateur
    }

    public function unblock(): void
    {
        $this->compte = 0; // Débloquer l'utilisateur
    }

}