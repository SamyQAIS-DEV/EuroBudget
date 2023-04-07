<?php

namespace App\Tests\Resource;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Resource\DepositAccountResource;
use PHPUnit\Framework\TestCase;

class DepositAccountResourceTest extends TestCase
{
    public function testFromDepositAccount(): void
    {
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getId')->willReturn(0);
        $depositAccount->method('getCreator')->willReturn((new User())->setId(0));
        $depositAccount->method('getAmount')->willReturn((float) 100);
        $resource = DepositAccountResource::fromDepositAccount($depositAccount, 5, 45);
        $this->assertEquals(100, $resource->amount);
        $this->assertEquals(45, $resource->waitingAmount);
        $this->assertEquals(55, $resource->finalAmount);
        $this->assertEquals(5, $resource->waitingOperationsNb);
    }

    public function testFromDepositAccountWithoutOperations(): void
    {
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getId')->willReturn(0);
        $depositAccount->method('getCreator')->willReturn((new User())->setId(0));
        $depositAccount->method('getAmount')->willReturn((float) 100);
        $resource = DepositAccountResource::fromDepositAccount($depositAccount, null, null);
        $this->assertEquals(100, $resource->amount);
        $this->assertEquals(0, $resource->waitingAmount);
        $this->assertEquals(100, $resource->finalAmount);
        $this->assertEquals(0, $resource->waitingOperationsNb);
    }
}
