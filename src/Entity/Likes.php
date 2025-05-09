<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LikesRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'likes')]
#[ORM\Index(name: 'liker_id', columns: ['liker_id'])]
#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Column(name: 'post_id')]
    #[Assert\NotBlank(message: 'Post ID cannot be blank.')]
    #[Assert\Type(type: 'integer', message: 'Post ID must be an integer.')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $postId = null;

    #[ORM\Column(name: 'liker_id')]
    #[Assert\NotBlank(message: 'Liker ID cannot be blank.')]
    #[Assert\Type(type: 'integer', message: 'Liker ID must be an integer.')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $likerId = null;

    #[ORM\Column(name: 'like_type', type: 'boolean')]
    #[Assert\NotNull(message: 'Like type cannot be null.')]
    private ?bool $likeType = null;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'Post_id', nullable: false, onDelete: 'CASCADE')]
    private ?Posts $post = null;

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): static
    {
        $this->postId = $postId;

        return $this;
    }

    public function getLikerId(): ?int
    {
        return $this->likerId;
    }

    public function setLikerId(int $likerId): static
    {
        $this->likerId = $likerId;

        return $this;
    }

    public function getLikeType(): ?bool
    {
        return $this->likeType;
    }

    public function setLikeType(bool $likeType): static
    {
        $this->likeType = $likeType;

        return $this;
    }
}
