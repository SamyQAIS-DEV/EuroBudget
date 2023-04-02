<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\PlanRepository;
use App\Tests\RepositoryTestCase;

/**
 * @property PlanRepository $repository
 */
class PlanRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = PlanRepository::class;

    /** @var User[] */
    private array $plans = [];

    public function testFindForAuthExistingEmail(): void
    {
        $this->plans = $this->loadFixtures(['plans']);
//        $this->assertSame(9, $this->repository->count([]));
//        $user = $this->plans['user1'];
//        $userFromRepo = $this->repository->findForAuth($user->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }

    public function testFindForOauthExistingEmail(): void
    {
        $this->plans = $this->loadFixtures(['plans']);
//        $this->assertSame(9, $this->repository->count([]));
//        $githubUser = $this->plans['github_user'];
//        $userFromRepo = $this->repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
//        $this->assertInstanceOf(User::class, $userFromRepo);
    }
}
