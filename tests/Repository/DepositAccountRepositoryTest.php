<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\DepositAccountRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property DepositAccountRepository $repository
 */
class DepositAccountRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = DepositAccountRepository::class;

    /** @var User[] */
    private array $depositAccounts = [];

    public function testFindForAuthExistingEmail(): void
    {
        $this->depositAccounts = $this->loadFixtures(['deposit-accounts']);
//        $this->assertSame(9, $this->repository->count([]));
//        $user = $this->depositAccounts['user1'];
//        $userFromRepo = $this->repository->findForAuth($user->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $this->depositAccounts = $this->loadFixtures(['deposit-accounts']);
//        $this->assertSame(9, $this->repository->count([]));
//        $githubUser = $this->depositAccounts['github_user'];
//        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
