<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigThemeExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigThemeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('body_theme', [TwigThemeExtensionRuntime::class, 'getUserTheme']),
        ];
    }
}
