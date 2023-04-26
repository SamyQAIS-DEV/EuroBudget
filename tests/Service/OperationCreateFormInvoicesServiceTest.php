<?php

namespace App\Tests\Service;

use App\Entity\Invoice;
use App\Entity\Operation;
use App\Entity\User;
use App\Service\OperationCreateFromInvoicesService;
use App\Tests\KernelTestCase;

class OperationCreateFormInvoicesServiceTest extends KernelTestCase
{
    private OperationCreateFromInvoicesService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(OperationCreateFromInvoicesService::class);
    }

    public function testProcess(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(Operation::class);
        $this->em->getRepository(Invoice::class)->save($this->getValidEntity($user), true);
        $originalNumObjectsInRepository = $repository->count([]);
        $this->service->process($user);
        self::assertSame($originalNumObjectsInRepository + 1, $repository->count([]));
    }

    private function getValidEntity(User $user): Invoice
    {
        return (new Invoice())
            ->setLabel('Valid label')
            ->setAmount(10)
            ->setCreator($user)
            ->setDepositAccount($user->getFavoriteDepositAccount());
    }
}