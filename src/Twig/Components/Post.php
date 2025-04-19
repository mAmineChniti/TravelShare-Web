<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Post
{
    public int $postId;
    public string $name;
    public string $lastName;
    public string $textContent;
    public bool $isLiked = false;
    public int $likesCount = 0;
    public array $comments = [];
}
