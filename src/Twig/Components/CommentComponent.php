<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('comment')]
class CommentComponent
{
    public string $commenter;
    public string $text;
}
