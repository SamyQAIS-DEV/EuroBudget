<?php

namespace App\Tests\Validator;

use App\Dto\TransferDto;
use App\Entity\DepositAccount;
use App\Tests\ValidatorTestCase;
use App\Validator\DifferentValidator;
use App\Validator\Different;
use App\Validator\Slug;

class DifferentValidatorTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        $constraint = new Different(fieldA: 'fromDepositAccount', fieldB: 'targetDepositAccount', entityClass: DepositAccount::class);
        $depositAccount1 = (new DepositAccount())->setId(1);
        $depositAccount2 = (new DepositAccount())->setId(2);
        $transfer1 = new TransferDto();
        $transfer1->fromDepositAccount = $depositAccount1;
        $transfer1->targetDepositAccount = $depositAccount2;
        $transfer2 = new TransferDto();
        $transfer2->fromDepositAccount = $depositAccount1;
        $transfer2->targetDepositAccount = $depositAccount1;
        yield [false, $transfer1, $constraint];
        yield [true, $transfer2, $constraint];
        yield [true, $transfer2, $constraint];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCategoryAccessConstraint(bool $expectViolation, $value, Different $constraint): void
    {
        $categoryAccessValidator = new DifferentValidator();
        $context = $this->getContext($expectViolation, 2);
        $categoryAccessValidator->initialize($context);
        $categoryAccessValidator->validate($value, $constraint);
    }
}
