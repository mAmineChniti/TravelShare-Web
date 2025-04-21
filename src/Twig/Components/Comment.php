<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Comment
{
    public int $commentId;
    public string $comment;
    public string $name;
    public string $lastName;
    public int $commenterId;
    public ?\DateTimeInterface $commentedAt = null;
    public ?\DateTimeInterface $updatedAt = null;
}
