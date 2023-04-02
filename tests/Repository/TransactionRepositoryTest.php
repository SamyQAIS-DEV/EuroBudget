<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property TransactionRepository $repository
 */
class TransactionRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = TransactionRepository::class;

    /** @var User[] */
    private array $transactions = [];

    public function testFindForAuthExistingEmail(): void
    {
        $this->transactions = $this->loadFixtures(['transactions']);
//        $this->assertSame(9, $this->repository->count([]));
//        $user = $this->transactions['user1'];
//        $userFromRepo = $this->repository->findForAuth($user->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $this->transactions = $this->loadFixtures(['transactions']);
//        $this->assertSame(9, $this->repository->count([]));
//        $githubUser = $this->transactions['github_user'];
//        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
