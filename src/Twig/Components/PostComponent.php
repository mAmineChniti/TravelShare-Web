<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Post')]
class PostComponent
{
    public string $posterName;
    public string $text;
    public string $likesCount;
    public array $comments;
    public bool $isLiked;
    public int $ownerId;
    public int $commentId;
}
