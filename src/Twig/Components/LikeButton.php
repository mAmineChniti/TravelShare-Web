<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class LikeButton
{
    public int $postId;
    public bool $isLiked = false;
    public int $likesCount = 0;
}
