<?php

namespace App\Tests\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Tests\RepositoryTestCase;
use DateTime;

/**
 * @property TransactionRepository $repository
 */
class TransactionRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = TransactionRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'transactions']);
    }

    public function testFindFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $transactions = $this->repository->findFor($user);
        $this->assertCount(1, $transactions);
        $this->assertInstanceOf(Transaction::class, $transactions[0]);
    }

    public function testGetMonthlyRevenues(): void
    {
        $revenues = $this->repository->getMonthlyRevenues();
        $now = new DateTime();
        $this->assertCount(1, $revenues);
        $this->assertSame((int) $now->format('n'), $revenues[0]['date']);
        $this->assertSame((int) $now->format('Ym'), $revenues[0]['fulldate']);
        $this->assertIsNumeric($revenues[0]['amount']);
    }
}
