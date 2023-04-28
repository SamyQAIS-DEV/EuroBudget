<?php

namespace App\Tests\Service;

use App\Dto\TransferDto;
use App\Entity\DepositAccount;
use App\Service\TransferService;
use App\Tests\KernelTestCase;

class TransferServiceTest extends KernelTestCase
{
    private TransferService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(TransferService::class);
    }

    public function testCreate(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);

        $originalTransfer = $this->getValidEntity($user1->getFavoriteDepositAccount(), $user2->getFavoriteDepositAccount());
        $transfer = $this->service->create($originalTransfer, $user1);
        $this->assertInstanceOf(TransferDto::class, $transfer);
        $this->assertSame($transfer->fromDepositAccount->getId(), $originalTransfer->fromDepositAccount->getId());
    }

    private function getValidEntity(DepositAccount $fromDepositAccount, DepositAccount $targetDepositAccount): TransferDto
    {
        $transfer = new TransferDto();
        $transfer->fromDepositAccount = $fromDepositAccount;
        $transfer->targetDepositAccount = $targetDepositAccount;
        $transfer->amount = 10;

        return $transfer;
    }
}