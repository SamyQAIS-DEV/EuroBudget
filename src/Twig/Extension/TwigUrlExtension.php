<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigUrlExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigUrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', [TwigUrlExtensionRuntime::class, 'avatarPath']),
        ];
    }
}
