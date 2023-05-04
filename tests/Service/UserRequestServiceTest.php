<?php

namespace App\Tests\Service;

use App\Service\UserRequestService;
use App\Tests\KernelTestCase;

class UserRequestServiceTest extends KernelTestCase
{
    private UserRequestService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(UserRequestService::class);
    }

    public function testCreate(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);
        $request = $this->service->create($user1, $user2, 'Bonjour', $user1->getFavoriteDepositAccount());
        $this->assertEquals($user1->getId(), $request->getCreator()->getId());
    }
}
