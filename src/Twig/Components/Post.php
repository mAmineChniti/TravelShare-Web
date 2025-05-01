<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Post
{
    public int $postId;
    public string $postTitle;
    public string $name;
    public string $lastName;
    public string $textContent;
    public ?bool $isLiked = null;
    public int $likesCount = 0;
    public int $dislikesCount = 0;
    public array $comments = [];
}
