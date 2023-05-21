<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\InactivityReminderService;
use App\Tests\KernelTestCase;
use DateTimeImmutable;

class InactivityReminderServiceTest extends KernelTestCase
{
    private UserRepository $userRepository;
    private InactivityReminderService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->service = self::getContainer()->get(InactivityReminderService::class);
    }

    public function testRemind(): void
    {
        $this->loadFixtures(['users']);
        $count = $this->service->remind();
        $this->assertEquals(0, $count);

        $fixture = (new User())->setEmail('inactive@domain.fr')->setLastname('lastname')->setFirstname('firstname')->setLastLoginAt(new DateTimeImmutable('-8 days'));
        $this->userRepository->save($fixture, true);

        $count = $this->service->remind();
        $this->assertEquals(1, $count);
    }
}
