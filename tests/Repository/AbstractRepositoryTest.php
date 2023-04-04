<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property UserRepository $repository
 */
class AbstractRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = UserRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users']);
    }

    public function testFindEncryptedPropertyExistingEmail(): void
    {
        $this->assertSame(9, $this->repository->count([]));
        $user = $this->data['user1'];
        $conditions = ['email' => $user->getEmail()];
        $usersFromRepo = $this->repository->findBy($conditions);
        $this->assertInstanceOf(User::class, $usersFromRepo[0]);
    }

    public function testFindOneEncryptedPropertyExistingEmail(): void
    {
        $this->assertSame(9, $this->repository->count([]));
        $user = $this->data['user1'];
        $conditions = ['email' => $user->getEmail()];
        $userFromRepo = $this->repository->findOneBy($conditions);
        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
