<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigUrlExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigUrlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [TwigUrlExtensionRuntime::class, 'urlFor']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', [TwigUrlExtensionRuntime::class, 'avatarPath']),
        ];
    }
}
