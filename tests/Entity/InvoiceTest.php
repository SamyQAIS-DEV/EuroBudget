<?php

namespace App\Tests\Entity;

use App\Entity\DepositAccount;
use App\Entity\Invoice;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidEntity(): void
    {
        $entity = $this->getValidEntity();
        $errors = $this->validator->validate($entity);
        $this->assertCount(0, $errors);
    }

    public function testInvalidMinLengthLabelEntity(): void
    {
        $entity = $this->getValidEntity()->setLabel('a');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop courte. Elle doit avoir au minimum 3 caractères.', $errors[0]->getMessage());
    }

    public function testInvalidMaxLengthLabelEntity(): void
    {
        $entity = $this->getValidEntity()->setLabel('azertyazertyazertyazertyazertyazertyazertyazertyazertyazerty');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 50 caractères.', $errors[0]->getMessage());
    }

    public function testInvalidPositiveAmountEntity(): void
    {
        $entity = $this->getValidEntity()->setAmount(-10);
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette valeur doit être strictement positive.', $errors[0]->getMessage());
    }

    private function getValidEntity(): Invoice
    {
        return (new Invoice())
            ->setLabel('Valid label')
            ->setAmount(10)
            ->setCreator($this->createMock(User::class))
            ->setDepositAccount($this->createMock(DepositAccount::class));
    }
}