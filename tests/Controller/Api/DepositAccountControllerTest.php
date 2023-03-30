<?php

namespace App\Tests\Controller\Api;

use App\Tests\WebTestCase;

class DepositAccountControllerTest extends WebTestCase
{
    public array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'deposit-accounts']);
    }

    public function testFavoriteRecap(): void
    {
        ['user1' => $user] = $this->data;
        $this->login($user);
        $content = $this->jsonRequest('GET', '/api/deposit-accounts/favorite-recap');
        $item = json_decode($content, null, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(0, $item->amount);
        $this->assertSame(0, $item->waitingOperationsNb);
        $this->assertSame(0, $item->waitingAmount);
        $this->assertSame(0, $item->finalAmount);
    }
}