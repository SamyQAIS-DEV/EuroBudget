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

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users']);
    }

    public function testCountFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $count = $this->repository->countFor($user);
        $this->assertSame(1, $count);
    }

    public function testFindFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $depositAccounts = $this->repository->findFor($user);
        $this->assertCount(1, $depositAccounts);
    }
}
