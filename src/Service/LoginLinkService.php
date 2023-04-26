<?php

namespace App\Service;

use App\Entity\LoginLink;
use App\Entity\User;
use App\Repository\LoginLinkRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class LoginLinkService
{
    public function __construct(
        private readonly TokenGeneratorService $generator,
        private readonly LoginLinkRepository $loginLinkRepository,
    ) {
    }

    public function createLoginLink(User $user): LoginLink
    {
        $this->loginLinkRepository->cleanFor($user);
        $loginLink = new LoginLink();

        $loginLink->setUser($user)
            ->setCreatedAt(new DateTimeImmutable())
            ->setToken($this->generator->generate());
        $this->loginLinkRepository->save($loginLink, true);

        return $loginLink;
    }
}