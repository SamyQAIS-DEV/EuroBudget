<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigAssetsExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigAssetsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('fix_is_attribute', [TwigAssetsExtensionRuntime::class, 'fixIsAttributeOnBrowsers']),
        ];
    }
}
