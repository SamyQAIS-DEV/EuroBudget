<?php

namespace App\Tests\Service;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Enum\TypeEnum;
use App\Exception\CalculatorException;
use App\Service\CalculatorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorServiceTest extends KernelTestCase
{
    private CalculatorService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(CalculatorService::class);
    }

    public function testDebitPastCreatedItem(): void
    {
        $operation = $this->getPastOperation(50);
        $this->assertEquals(-50, $this->service->calculate($operation));
    }

    public function testDebitNotPastCreatedItem(): void
    {
        $operation = $this->getOperation(50);
        $this->assertEquals(0, $this->service->calculate($operation));
    }

    public function testDebitPastUpdatedItemDebitPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50);
        $originalOperation = $this->getPastOperation(80);
        $this->assertEquals(30, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90);
        $originalOperation = $this->getPastOperation(30);
        $this->assertEquals(-60, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitPastUpdatedItemCreditPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50);
        $originalOperation = $this->getPastOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-130, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90);
        $originalOperation = $this->getPastOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-120, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitPastUpdatedItemDebitNotPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50);
        $originalOperation = $this->getOperation(80);
        $this->assertEquals(-50, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90);
        $originalOperation = $this->getOperation(30);
        $this->assertEquals(-90, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitPastUpdatedItemCreditNotPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50);
        $originalOperation = $this->getOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-50, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90);
        $originalOperation = $this->getOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-90, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitNotPastUpdatedItemDebitPastOriginalItem(): void
    {
        $operation = $this->getOperation(50);
        $originalOperation = $this->getPastOperation(80);
        $this->assertEquals(80, $this->service->calculate($operation, $originalOperation));


        $operation = $this->getOperation(90);
        $originalOperation = $this->getPastOperation(30);
        $this->assertEquals(30, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitNotPastUpdatedItemCreditPastOriginalItem(): void
    {
        $operation = $this->getOperation(50);
        $originalOperation = $this->getPastOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-80, $this->service->calculate($operation, $originalOperation));


        $operation = $this->getOperation(90);
        $originalOperation = $this->getPastOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-30, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitNotPastUpdatedItemDebitNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50);
        $originalOperation = $this->getOperation(80);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90);
        $originalOperation = $this->getOperation(30);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitNotPastUpdatedItemCreditNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50);
        $originalOperation = $this->getOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90);
        $originalOperation = $this->getOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));
    }

    public function testDebitDeletedItemPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50);
        $this->assertEquals(50, $this->service->calculate(null, $operation));
    }

    public function testDebitDeletedItemNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50);
        $this->assertEquals(0, $this->service->calculate(null, $operation));
    }

    public function testCreditPastCreatedItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $this->assertEquals(50, $this->service->calculate($operation));
    }

    public function testCreditNotPastCreatedItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate($operation));
    }

    public function testCreditPastUpdatedItemDebitPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(80);
        $this->assertEquals(130, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(30);
        $this->assertEquals(120, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditPastUpdatedItemCreditPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-30, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(60, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditPastUpdatedItemDebitNotPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(80);
        $this->assertEquals(50, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(30);
        $this->assertEquals(90, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditPastUpdatedItemCreditNotPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(50, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getPastOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(90, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditNotPastUpdatedItemDebitPastOriginalItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(80);
        $this->assertEquals(80, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(30);
        $this->assertEquals(30, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditNotPastUpdatedItemCreditPastOriginalItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-80, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getPastOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-30, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditNotPastUpdatedItemDebitNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(80);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(30);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditNotPastUpdatedItemCreditNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(80)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));

        $operation = $this->getOperation(90)->setType(TypeEnum::CREDIT);
        $originalOperation = $this->getOperation(30)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate($operation, $originalOperation));
    }

    public function testCreditDeletedItemPastOriginalItem(): void
    {
        $operation = $this->getPastOperation(50)->setType(TypeEnum::CREDIT);
        $this->assertEquals(-50, $this->service->calculate(null, $operation));
    }

    public function testCreditDeletedItemNotPastOriginalItem(): void
    {
        $operation = $this->getOperation(50)->setType(TypeEnum::CREDIT);
        $this->assertEquals(0, $this->service->calculate(null, $operation));
    }

    private function getPastOperation(float $amount): Operation
    {
        return $this->getOperation($amount)->setPast(true);
    }

    private function getOperation($amount): Operation
    {
        $operation = new Operation();
        $operation->setPast(false)
            ->setAmount($amount)
            ->setType(TypeEnum::DEBIT);

        return $operation;
    }
}