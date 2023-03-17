<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [TwigExtensionRuntime::class, 'svgIcon'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [TwigExtensionRuntime::class, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }
}
