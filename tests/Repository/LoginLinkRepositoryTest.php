<?php

namespace App\Tests\Repository;

use App\Entity\LoginLink;
use App\Repository\LoginLinkRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property LoginLinkRepository $repository
 */
class LoginLinkRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = LoginLinkRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['login-links']);
    }

    public function testCleanFor(): void
    {
        /** @var LoginLink $loginLink */
        $loginLink = $this->data['user1_login_link'];
        $this->assertSame(2, $this->repository->count([]));
        $user = $loginLink->getUser();
        $this->repository->cleanFor($user);
        $this->assertSame(1, $this->repository->count([]));
    }
}
