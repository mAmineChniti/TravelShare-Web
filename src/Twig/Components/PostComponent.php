<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('post')]
class PostComponent
{
    public string $posterName;
    public string $text;
    public string $likesCount;
    public array $comments;
    public bool $isLiked;
}
