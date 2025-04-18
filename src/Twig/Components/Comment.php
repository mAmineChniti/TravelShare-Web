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
    public ?\DateTimeImmutable $commentedAt = null;
    public ?\DateTimeImmutable $updatedAt = null;
}