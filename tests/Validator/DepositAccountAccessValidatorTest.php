<?php

namespace App\Tests\Validator;

use App\Dto\TransferDto;
use App\Entity\CategorizableInterface;
use App\Entity\DepositAccount;
use App\Entity\Invoice;
use App\Entity\User;
use App\Exception\UnauthenticatedException;
use App\Tests\ValidatorTestCase;
use App\Validator\DepositAccountAccess;
use App\Validator\DepositAccountAccessValidator;
use Symfony\Bundle\SecurityBundle\Security;

class DepositAccountAccessValidatorTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        $constraint = new DepositAccountAccess();
        $user = new User();
        $depositAccount1 = new DepositAccount();
        $depositAccount2 = new DepositAccount();
        $depositAccount1->addUser($user);
        yield [false, $depositAccount1, $user, $constraint];
        yield [true, $depositAccount2, $user, $constraint];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDepositAccountAccessConstraint(bool $expectViolation, $value, User $user, DepositAccountAccess $constraint): void
    {
        $security = $this->createMock(Security::class);
        $security->expects($this->once())->method('getUser')->willReturn($user);
        $categoryAccessValidator = new DepositAccountAccessValidator($security);
        $context = $this->getContext($expectViolation);
        $categoryAccessValidator->initialize($context);
        $categoryAccessValidator->validate($value, $constraint);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDepositAccountAccessConstraintUserUnauthenticated(bool $expectViolation, $value, User $user, DepositAccountAccess $constraint): void
    {
        $security = $this->createMock(Security::class);
        $security->expects($this->once())->method('getUser')->willReturn(null);
        $categoryAccessValidator = new DepositAccountAccessValidator($security);
        $context = $this->getContext(false);
        $categoryAccessValidator->initialize($context);
        $this->expectException(UnauthenticatedException::class);
        $categoryAccessValidator->validate($value, $constraint);
    }
}
