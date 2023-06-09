<?php

namespace App\Tests\Service;

use App\Entity\LoginLink;
use App\Entity\User;
use App\Service\LoginLinkService;
use App\Service\TokenGeneratorService;
use App\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class LoginLinkServiceTest extends KernelTestCase
{
    private LoginLinkService $loginLinkService;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginLinkService = self::getContainer()->get(LoginLinkService::class);
    }

    public function testCreateLoginLink(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $loginLink = $this->loginLinkService->createLoginLink($user);
        $this->assertInstanceOf(LoginLink::class, $loginLink);
    }

    public function testCreateAlreadyExistingLoginLink(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users', 'login-links']);
        $loginLink = $this->loginLinkService->createLoginLink($user);
        $this->assertInstanceOf(LoginLink::class, $loginLink);
    }
}
