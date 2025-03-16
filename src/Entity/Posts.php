<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'posts')]
#[ORM\Index(name: 'fk_owner_id', columns: ['Owner_id'])]
#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Column(name: "Post_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $postId = null;

    #[ORM\Column(name: "Owner_id")]
    private ?int $ownerId = null;

    #[ORM\Column(name: "created_at", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: "updated_at", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: "text_content", length: 255)]
    private ?string $textContent = null;

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(string $textContent): static
    {
        $this->textContent = $textContent;

        return $this;
    }
}
