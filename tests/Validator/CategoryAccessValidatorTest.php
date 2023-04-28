<?php

namespace App\Tests\Validator;

use App\Entity\CategorizableInterface;
use App\Entity\Category;
use App\Entity\DepositAccount;
use App\Entity\Invoice;
use App\Tests\ValidatorTestCase;
use App\Validator\CategoryAccess;
use App\Validator\CategoryAccessValidator;
use App\Validator\Slug;
use Symfony\Component\Validator\Constraints\RegexValidator;

class CategoryAccessValidatorTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        $constraint = new CategoryAccess();
        $category = new Category();
        $depositAccount1 = new DepositAccount();
        $depositAccount2 = new DepositAccount();
        $depositAccount1->addCategory($category);
        $invoice1 = (new Invoice())->setDepositAccount($depositAccount1);
        $invoice2 = (new Invoice())->setDepositAccount($depositAccount2);
        yield [false, $category, $invoice1, $constraint];
        yield [true, $category, $invoice2, $constraint];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCategoryAccessConstraint(bool $expectViolation, $value, CategorizableInterface $categorizable, CategoryAccess $constraint): void
    {
        $categoryAccessValidator = new CategoryAccessValidator();
        $context = $this->getContext($expectViolation);
        $context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($categorizable);
        $categoryAccessValidator->initialize($context);
        $categoryAccessValidator->validate($value, $constraint);
    }
}
