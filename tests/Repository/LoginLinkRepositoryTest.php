<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\LoginLinkRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property LoginLinkRepository $repository
 */
class LoginLinkRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = LoginLinkRepository::class;

    /** @var User[] */
    private array $loginLinks = [];

    public function testCleanFor(): void
    {
//        $this->loginLinks = $this->loadFixtures(['login-links']);
//        $this->assertSame(9, $this->repository->count([]));
//        $user = $this->loginLinks['user1'];
//        $userFromRepo = $this->repository->findForAuth($user->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
