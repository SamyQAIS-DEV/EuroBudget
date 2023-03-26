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

    /** @var User[] */
    private array $users = [];

    public function testFindForAuthExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(8, $this->repository->count([]));
        $user = $this->users['user1'];
        $userFromRepo = $this->repository->findForAuth($user->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(8, $this->repository->count([]));
        $githubUser = $this->users['github_user'];
        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
