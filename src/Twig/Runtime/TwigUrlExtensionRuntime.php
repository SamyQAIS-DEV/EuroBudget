<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Twig\Extension\RuntimeExtensionInterface;

class TwigUrlExtensionRuntime implements RuntimeExtensionInterface
{
    public function avatarPath(User $user): string
    {
        return '/images/default.png'; // TODO
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
