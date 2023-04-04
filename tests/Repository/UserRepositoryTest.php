<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property UserRepository $repository
 */
class UserRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = UserRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users']);
    }

    public function testFindForAuthExistingEmail(): void
    {
        $user = $this->data['user1'];
        $this->assertSame(9, $this->repository->count([]));
        $userFromRepo = $this->repository->findForAuth($user->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $githubUser = $this->data['github_user'];
        $this->assertSame(9, $this->repository->count([]));
        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
