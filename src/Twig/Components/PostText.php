<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class PostText
{
    public int $postId;
    public string $postTitle;
    public string $textContent;
}
