<?php

namespace App\Tests\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\InvoiceRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property InvoiceRepository $repository
 */
class InvoiceRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = InvoiceRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'invoices']);
    }

    public function testFindByDepositAccount(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $invoices = $this->repository->findByDepositAccount($user->getFavoriteDepositAccount());
        $this->assertCount(1, $invoices);
        $this->assertInstanceOf(Invoice::class, $invoices[0]);
    }
}
