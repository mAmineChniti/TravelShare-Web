<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Footer
{
    public int $year;

    public function __construct()
    {
        $this->year = (int) date('Y');
    }
}