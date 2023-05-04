<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserRequest;
use App\Tests\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRequestTest extends WebTestCase
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

    public function testInvalidBlankCreatorEntity(): void
    {
        $user = (new User())->setId(1);
        $entity = (new UserRequest())->setTarget($user);
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
    }

    public function testInvalidBlankTargetEntity(): void
    {
        $user = (new User())->setId(1);
        $entity = (new UserRequest())->setTarget($user);
        $errors = $this->validator->validate($entity);
        $this->assertCount(1, $errors);
    }

    public function testIsAnswered(): void
    {
        $unanswered = $this->getValidEntity();
        $this->assertFalse($unanswered->isAnswered());

        $accepted = $this->getValidEntity()->setAccepted(true);
        $this->assertTrue($accepted->isAnswered());

        $rejected = $this->getValidEntity()->setRejected(true);
        $this->assertTrue($rejected->isAnswered());
    }

    private function getValidEntity(): UserRequest
    {
        return (new UserRequest())
            ->setCreator((new User())->setId(1))
            ->setTarget((new User())->setId(2));
    }
}