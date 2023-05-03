<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelperInterface;

class TwigUrlExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UploaderHelperInterface $uploaderHelper,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @param string|object $path
     */
    public function urlFor($path, array $params = []): string
    {
        return $this->urlGenerator->generate(
            $path,
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function avatarPath(User $user): string
    {
        if ($user->getAvatarName() === null) {
            return '/images/default.png';
        }

        return sprintf(
            '%s?uid=%s',
            $this->uploaderHelper->asset($user, 'avatarFile'),
            $user->getUpdatedAt()?->getTimestamp() ?: 0
        );
    }
}
