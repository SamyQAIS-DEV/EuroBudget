<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class TwigAssetsExtensionRuntime implements RuntimeExtensionInterface
{
    private bool $polyfillLoaded = false;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function fixIsAttributeOnBrowsers(): string
    {
        $script = "";
        $request = $this->requestStack->getCurrentRequest();

        if (false === $this->polyfillLoaded && $request instanceof Request) {
            $userAgent = $request->headers->get('User-Agent') ?: '';
            if (strpos($userAgent, 'Safari') &&
                !strpos($userAgent, 'Chrome')) {
                $this->polyfillLoaded = true;
                $script = <<<HTML
                    <script src="/document-register-element.js" defer></script>
                HTML;
            }
        }

        return $script;
    }
}
