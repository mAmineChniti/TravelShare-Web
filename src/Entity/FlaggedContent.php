<?php

namespace App\Entity;

use App\Repository\FlaggedContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'flagged_content')]
#[ORM\Index(name: 'fk_flagger_id', columns: ['flagger_id'])]
#[ORM\Entity(repositoryClass: FlaggedContentRepository::class)]
class FlaggedContent
{
    #[ORM\Column(name: "post_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?int $postId = null;

    #[ORM\Column(name: "flagger_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?int $flaggerId = null;

    #[ORM\Column(name: "flagged_at", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $flaggedAt = null;

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): static
    {
        $this->postId = $postId;

        return $this;
    }

    public function getFlaggerId(): ?int
    {
        return $this->flaggerId;
    }

    public function setFlaggerId(int $flaggerId): static
    {
        $this->flaggerId = $flaggerId;

        return $this;
    }

    public function getFlaggedAt(): ?\DateTimeInterface
    {
        return $this->flaggedAt;
    }

    public function setFlaggedAt(\DateTimeInterface $flaggedAt): static
    {
        $this->flaggedAt = $flaggedAt;

        return $this;
    }
}
