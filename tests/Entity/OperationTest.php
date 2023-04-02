<?php

namespace App\Tests\Entity;

use App\Entity\Operation;
use App\Tests\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidEntity(): void
    {
    }

    public function testInvalidBlankLabelEntity(): void
    {
    }

    public function testInvalidBlankAmountEntity(): void
    {
    }

    private function getValidEntity(): Operation
    {
        return (new Operation())
            ->setLabel('Valid label');
    }
}