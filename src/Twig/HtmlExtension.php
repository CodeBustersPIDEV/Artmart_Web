<?php
// src/Twig/HtmlExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HtmlExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('html', [$this, 'renderHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function renderHtml($text)
    {
        return $text;
    }
}
