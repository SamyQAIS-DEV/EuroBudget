<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use Twig\TwigFunction;

class TwigExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * Génère le code HTML pour une icone SVG.
     */
    public function svgIcon(string $name, ?int $size = null): string
    {
        $attrs = '';
        if ($size) {
            $attrs = " width=\"{$size}px\" height=\"{$size}px\"";
        }

        return <<<HTML
        <svg class="icon icon-{$name}"{$attrs}>
            <use href="/sprite.svg#{$name}"></use>
        </svg>
        HTML;
    }

    /**
     * Ajout une class is-active pour les éléments actifs du menu.
     *
     * @param array<string,mixed> $context
     */
    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return ' aria-current="page"';
        }

        return '';
    }
}
