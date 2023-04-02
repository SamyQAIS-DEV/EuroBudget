<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\OperationRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property OperationRepository $repository
 */
class OperationRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = OperationRepository::class;

    /** @var User[] */
    private array $operations = [];

    public function testFindForAuthExistingEmail(): void
    {
        $this->operations = $this->loadFixtures(['operations']);
//        $this->assertSame(9, $this->repository->count([]));
//        $user = $this->operations['user1'];
//        $userFromRepo = $this->repository->findForAuth($user->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $this->operations = $this->loadFixtures(['operations']);
//        $this->assertSame(9, $this->repository->count([]));
//        $githubUser = $this->operations['github_user'];
//        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
