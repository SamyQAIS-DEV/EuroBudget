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

    public function testCountFor(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $count = $this->repository->countFor($user);
        $this->assertSame(1, $count);
    }

    public function testFindFor(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $depositAccounts = $this->repository->findFor($user);
        $this->assertCount(1, $depositAccounts);
    }

    public function testFindForAndWithout(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);
        $originalDepositAccounts = $this->repository->findForAndWithout($user1, $user2);
        $this->assertCount(1, $originalDepositAccounts);
        $depositAccount = $user1->getFavoriteDepositAccount();
        $depositAccount->addUser($user2);
        $this->repository->save($depositAccount, true);
        $depositAccounts = $this->repository->findForAndWithout($user1, $user2);
        $this->assertCount(0, $depositAccounts);
    }
}
