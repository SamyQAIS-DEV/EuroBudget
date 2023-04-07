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
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenGeneratorService $generator,
        private readonly LoginLinkRepository $loginLinkRepository,
    ) {
    }

    public function createLoginLink(User $user): LoginLink
    {
        $this->loginLinkRepository->cleanFor($user);
        $loginLink = new LoginLink();
        $this->entityManager->persist($loginLink);

        $loginLink->setUser($user)
            ->setCreatedAt(new DateTimeImmutable())
            ->setToken($this->generator->generate());
        $this->entityManager->flush();

        return $loginLink;
    }
}