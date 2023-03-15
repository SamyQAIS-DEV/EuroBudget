<?php

namespace App\Tests\Service;

use App\Entity\LoginLinkToken;
use App\Entity\User;
use App\Service\LoginLinkService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LoginLinkServiceTest extends TestCase
{
    private LoginLinkService $service;

    public function setUp(): void
    {
        parent::setUp();
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $generator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();

        $this->service = new LoginLinkService(
            $entityManager,
            $generator
        );
        parent::setUp();
    }

    public function testCreateLoginLink(): void
    {
        $user = $this->createMock(User::class);
        $loginLink = $this->service->createLoginLink($user);
        $this->assertInstanceOf(LoginLinkToken::class, $loginLink);
    }
}
