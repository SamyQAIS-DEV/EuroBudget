<?php

namespace App\Tests\Entity;

use App\Entity\DepositAccount;
use App\Entity\Category;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryTest extends WebTestCase
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

    public function testInvalidMinLengthNameEntity(): void
    {
        $entity = $this->getValidEntity()->setName('a');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop courte. Elle doit avoir au minimum 3 caractères.', $errors[0]->getMessage());
    }

    public function testInvalidMaxLengthNameEntity(): void
    {
        $entity = $this->getValidEntity()->setName('azertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazera');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 180 caractères.', $errors[0]->getMessage());
    }

    public function testInvalidMinLengthSlugEntity(): void
    {
        $entity = $this->getValidEntity()->setSlug('a');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop courte. Elle doit avoir au minimum 3 caractères.', $errors[0]->getMessage());
    }

    public function testInvalidMaxLengthSlugEntity(): void
    {
        $entity = $this->getValidEntity()->setSlug('azertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazertyazerazerty');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.', $errors[0]->getMessage());
    }

    private function getValidEntity(): Category
    {
        return (new Category())
            ->setName('Valid Name')
            ->setSlug('Valid Name')
            ->setOwner($this->createMock(User::class))
            ->setDepositAccount($this->createMock(DepositAccount::class));
    }
}