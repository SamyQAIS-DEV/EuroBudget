<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigEmailExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigEmailExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_email', [TwigEmailExtensionRuntime::class, 'markdownEmail'], [
                'needs_context' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('text_email', [TwigEmailExtensionRuntime::class, 'formatText']),
        ];
    }
}
