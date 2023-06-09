<?php

namespace App\Tests\Service;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Entity\User;
use App\Service\OperationService;
use App\Tests\KernelTestCase;

class OperationServiceTest extends KernelTestCase
{
    private OperationService $service;

    private DepositAccount $depositAccount;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(OperationService::class);
        $this->depositAccount = $this->createMock(DepositAccount::class);
    }

    public function testCreate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $operation = $this->service->create($this->getValidOperation(), $user, $user->getFavoriteDepositAccount());
        $this->assertInstanceOf(Operation::class, $operation);
        $this->assertSame($user->getFavoriteDepositAccount()->getId(), $operation->getDepositAccount()->getId());
    }

    public function testUpdate(): void
    {
        /** @var Operation $operation */
        ['operation1' => $originalOperation] = $this->loadFixtures(['operations']);
        $operation = clone $originalOperation;
        $operation->setLabel('Updated !!!');
        $operation->setAmount(250);
        $operation = $this->service->update($operation, $originalOperation);
        $this->assertInstanceOf(Operation::class, $operation);
        $this->assertSame('Updated !!!', $operation->getLabel());
    }

    public function testDelete(): void
    {
        /** @var Operation $operation */
        ['operation1' => $operation] = $this->loadFixtures(['operations']);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $this->service->delete($operation);
        $this->assertInstanceOf(Operation::class, $operation);
        $this->assertSame(9, $this->em->getRepository(Operation::class)->count([]));
    }


    private function getValidOperation(): Operation
    {
        return (new Operation())
            ->setLabel('label')
            ->setAmount(10);
    }
}