<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\UserRequest;
use App\Repository\UserRequestRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property UserRequestRepository $repository
 */
class UserRequestRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = UserRequestRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'user-requests']);
    }

    public function testFindFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $requests = $this->repository->findFor($user);
        $this->assertCount(1, $requests);
        $this->assertInstanceOf(UserRequest::class, $requests[0]);
    }
}
