<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class VoteButtons
{
    public int $postId;
    public ?bool $isLiked = null;
    public int $likesCount = 0;
    public int $dislikesCount = 0;
}
