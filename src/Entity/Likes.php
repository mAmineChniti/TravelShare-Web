<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LikesRepository;

#[ORM\Table(name: 'likes')]
#[ORM\Index(name: 'liker_id', columns: ['liker_id'])]
#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Column(name: 'post_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $postId = null;

    #[ORM\Column(name: 'liker_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $likerId = null;

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
}
