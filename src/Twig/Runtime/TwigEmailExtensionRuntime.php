<?php

namespace App\Twig\Runtime;

use Parsedown;
use Twig\Extension\RuntimeExtensionInterface;

class TwigEmailExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * Convertit le contenu markdown en HTML.
     */
    public function markdownEmail(array $context, string $content): string
    {
        if (($context['format'] ?? 'text') === 'text') {
            return $content;
        }
        $content = preg_replace('/^(^ {2,})(\S+[ \S]*)$/m', '${2}', $content);
        $content = (new Parsedown())->setSafeMode(false)->text($content);

        return $content;
    }

    public function formatText(string $content): string
    {
        $content = strip_tags($content);
        $content = preg_replace('/^(^ {2,})(\S+[ \S]*)$/m', '${2}', $content) ?: '';
        $content = preg_replace("/([\r\n] *){3,}/", "\n\n", $content) ?: '';

        return $content;
    }
}
