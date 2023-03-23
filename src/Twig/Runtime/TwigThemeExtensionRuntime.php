<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class TwigThemeExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getUserTheme(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            return (string) $request->cookies->get('theme');
        }

        return '';
    }
}
