<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property UserRepository $repository
 */
class UserRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = UserRepository::class;

    public function testFindForAuthExistingEmail(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->assertSame(9, $this->repository->count([]));
        $userFromRepo = $this->repository->findForAuth($user->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        ['github_user' => $githubUser] = $this->loadFixtures(['users']);
        $this->assertSame(9, $this->repository->count([]));
        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testSearch(): void
    {
        $this->loadFixtures(['users']);
        $this->assertSame(9, $this->repository->count([]));
        $users = $this->repository->search('Firstname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('firstname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('FIRSTNAME1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('Lastname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('lastname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('LASTNAME1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('Firstname1 Lastname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('Firstname1 lastname1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('Firstname1 LASTNAME1');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('Firstname1', $users[0]->getFirstname());

        $users = $this->repository->search('user1@domain.fr');
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertSame('user1@domain.fr', $users[0]->getEmail());

        $users = $this->repository->search('Firstname1 Lastnerkvnjernvkjrnvame1');
        $this->assertCount(0, $users);
    }

    public function testFindOneByRole(): void
    {
        $this->loadFixtures(['users']);
        $this->assertSame(9, $this->repository->count([]));
        $userFromRepo = $this->repository->findOneByRole('ROLE_ADMIN');
        $this->assertInstanceOf(User::class, $userFromRepo);
        $this->assertContains('ROLE_ADMIN', $userFromRepo->getRoles());
    }
}
