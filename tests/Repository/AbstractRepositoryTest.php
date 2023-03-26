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

    /** @var User[] */
    private array $users = [];

    public function testFindEncryptedPropertyExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(8, $this->repository->count([]));
        $user = $this->users['user1'];
        $conditions = ['email' => $user->getEmail()];
        $usersFromRepo = $this->repository->findBy($conditions);
        $this->assertInstanceOf(User::class, $usersFromRepo[0]);
    }

    public function testFindOneEncryptedPropertyExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(8, $this->repository->count([]));
        $user = $this->users['user1'];
        $conditions = ['email' => $user->getEmail()];
        $userFromRepo = $this->repository->findOneBy($conditions);
        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
