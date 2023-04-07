<?php

namespace App\Tests\Repository;

use App\Entity\Operation;
use App\Entity\User;
use App\Repository\OperationRepository;
use App\Tests\RepositoryTestCase;
use DateTime;

/**
 * @property OperationRepository $repository
 */
class OperationRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = OperationRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'operations']);
    }

    public function testFindForRecap(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $recap = $this->repository->findForRecap($user->getFavoriteDepositAccount()->getId());
        $this->assertSame(2, $recap['waitingOperationsNb']);
        $this->assertIsNumeric($recap['waitingAmount']);
    }

    public function testFindForYearAndMonth(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $now = new DateTime();
        $operations = $this->repository->findForYearAndMonth($user->getFavoriteDepositAccount()->getId(), (int) $now->format('Y'), (int) $now->format('m'));
        $this->assertCount(1, $operations);
    }

    public function testFindYearsMonths(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $yearsMonths = $this->repository->findYearsMonths($user->getFavoriteDepositAccount()->getId());
        $now = new DateTime();
        $this->assertCount(2, $yearsMonths);
        $this->assertSame($now->format('Y/m'), $yearsMonths[0]['path']);
        $this->assertSame(1, $yearsMonths[0]['count']);
    }

    public function testFindYearsMonthsWithoutOperations(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $yearsMonths = $this->repository->findYearsMonths($user->getFavoriteDepositAccount()->getId());
        $this->assertEmpty($yearsMonths);
    }

    public function testCountForYearAndMonth(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $now = new DateTime();
        $operationsCount = $this->repository->countForYearAndMonth($user->getFavoriteDepositAccount()->getId(), (int) $now->format('Y'), (int) $now->format('m'));
        $this->assertSame(1, $operationsCount);
    }
}
