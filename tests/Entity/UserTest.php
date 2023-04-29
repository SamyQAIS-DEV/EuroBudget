<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends WebTestCase
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

    public function testInvalidBlankEmailEntity(): void
    {
        $entity = $this->getValidEntity()->setEmail('');
        $errors = $this->validator->validate($entity);
        $this->assertCount(2, $errors);
    }

    public function testInvalidUsedEmailEntity(): void
    {
        $this->loadFixtures(['users']);
        $entity = $this->getValidEntity()->setEmail('user1@domain.fr');
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
    }

    private function getValidEntity(): User
    {
        return (new User())
            ->setEmail('validuser@domain.fr')
            ->setLastname('Lastname')
            ->setFirstname('Firstname');
    }
}