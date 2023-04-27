<?php

namespace App\Tests\Controller\Api;

use App\Tests\WebTestCase;

class CategoryControllerTest extends WebTestCase
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
        $content = $this->jsonRequest('GET', '/api/categories');
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(0, $items);
    }
}