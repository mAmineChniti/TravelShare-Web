<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentsRepository;

#[ORM\Table(name: 'comments')]
#[ORM\Index(name: 'fk_commenter_id', columns: ['commenter_id'])]
#[ORM\Index(name: 'fk_comment_post_id', columns: ['post_id'])]
#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Column(name: 'comment_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $commentId = null;

    #[ORM\Column(name: 'post_id')]
    private ?int $postId = null;

    #[ORM\Column(name: 'commenter_id')]
    private ?int $commenterId = null;

    #[ORM\Column(name: 'comment', length: 255)]
    private ?string $comment = null;

    #[ORM\Column(name: 'commented_at', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $commentedAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getCommentId(): ?int
    {
        return $this->commentId;
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

    public function getCommenterId(): ?int
    {
        return $this->commenterId;
    }

    public function setCommenterId(int $commenterId): static
    {
        $this->commenterId = $commenterId;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentedAt(): ?\DateTimeInterface
    {
        return $this->commentedAt;
    }

    public function setCommentedAt(\DateTimeInterface $commentedAt): static
    {
        $this->commentedAt = $commentedAt;

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
}
