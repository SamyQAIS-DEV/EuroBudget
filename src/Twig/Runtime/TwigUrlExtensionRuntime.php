<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Twig\Extension\RuntimeExtensionInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelperInterface;

class TwigUrlExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly UploaderHelperInterface $uploaderHelper
    ) {
    }

    public function avatarPath(User $user): string
    {
        if (null === $user->getAvatarName()) {
            return '/images/default.png';
        }

        return sprintf(
            '%s?uid=%s',
            $this->uploaderHelper->asset($user, 'avatarFile'),
            $user->getUpdatedAt()?->getTimestamp() ?: 0
        );
    }
}
