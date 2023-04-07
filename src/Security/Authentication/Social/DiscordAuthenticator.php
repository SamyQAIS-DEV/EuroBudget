<?php

namespace App\Security\Authentication\Social;

use App\Entity\User;
use App\Exception\NotVerifiedEmailException;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'discord';

    public function getUserFromResourceOwner(ResourceOwnerInterface $discordUser, UserRepository $repository): ?User
    {
        if (!($discordUser instanceof DiscordResourceOwner)) {
            throw new \RuntimeException('Expecting DiscordResourceOwner as the first parameter');
        }
        if (true !== ($discordUser->toArray()['verified'] ?? null)) {
            throw new NotVerifiedEmailException();
        }
        $user = $repository->findForOauth('discord', $discordUser->getId(), $discordUser->getEmail());
        if ($user && null === $user->getDiscordId()) {
            $user->setDiscordId($discordUser->getId());
            $this->entityManager->flush();
        }

        return $user;
    }
}
