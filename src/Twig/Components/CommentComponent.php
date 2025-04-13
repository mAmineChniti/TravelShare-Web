<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Comment')]
class CommentComponent
{
    public int $commentId;
    public string $commenter;
    public string $text;
    public int $commenterId;
}
