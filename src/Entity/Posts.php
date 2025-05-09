<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostsRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'posts')]
#[ORM\Index(name: 'fk_owner_id', columns: ['Owner_id'])]
#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Column(name: 'Post_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $postId = null;

    #[ORM\Column(name: 'Owner_id')]
    #[Assert\NotBlank(message: 'Owner ID cannot be blank.')]
    #[Assert\Type(type: 'integer', message: 'Owner ID must be an integer.')]
    private ?int $ownerId = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Created at cannot be blank.')]
    #[Assert\Type(type: \DateTimeInterface::class, message: 'Created at must be a valid datetime.')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Updated at cannot be blank.')]
    #[Assert\Type(type: \DateTimeInterface::class, message: 'Updated at must be a valid datetime.')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'text_content', length: 255)]
    #[Assert\NotBlank(message: 'Text content cannot be blank.')]
    #[Assert\Length(
        min: 15,
        max: 255,
        minMessage: 'Text content must be at least 15 characters long.',
        maxMessage: 'Text content cannot be longer than 255 characters.'
    )]
    private ?string $textContent = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Post title cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Post title cannot be longer than 255 characters.'
    )]
    private ?string $postTitle = null;

    #[Gedmo\Slug(fields: ['postTitle', 'postUnique'])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $postUnique = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostImages::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comments::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Likes::class, cascade: ['remove'])]
    private Collection $likes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

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

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): static
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPostUnique(): ?string
    {
        return $this->postUnique;
    }

    public function setPostUnique(string $postUnique): static
    {
        $this->postUnique = $postUnique;

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PostImages $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(PostImages $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    #[ORM\PreRemove]
    public function deleteAssociatedImages(): void
    {
        foreach ($this->images as $image) {
            $image->setPost(null);
        }
        $this->images->clear();
    }

    #[ORM\PreRemove]
    public function deleteAssociatedComments(): void
    {
        foreach ($this->comments as $comment) {
            $comment->setPost(null);
        }
        $this->comments->clear();
    }

    #[ORM\PreRemove]
    public function deleteAssociatedLikes(): void
    {
        foreach ($this->likes as $like) {
            $like->setPost(null);
        }
        $this->likes->clear();
    }
}
