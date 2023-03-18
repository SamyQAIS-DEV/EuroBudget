<?php

namespace App\Service;

use App\Entity\LoginLinkToken;
use App\Entity\User;
use App\Repository\LoginLinkTokenRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class LoginLinkService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenGeneratorService $generator,
        private readonly LoginLinkTokenRepository $loginLinkTokenRepository,
    ) {
    }

    public function createLoginLink(User $user): LoginLinkToken
    {
        $this->loginLinkTokenRepository->cleanByUser($user);
        $loginLink = new LoginLinkToken();
        $this->entityManager->persist($loginLink);

        $loginLink->setUser($user)
            ->setCreatedAt(new DateTime())
            ->setToken($this->generator->generate());
        $this->entityManager->flush();

        return $loginLink;
    }
}