<?php

namespace App\Tests\Controller\Api;

use App\Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users']);
    }

    // TODO
//    public function testSearch(): void
//    {
//        ['user1' => $user] = $this->data;
//        $this->login($user);
//        $content = $this->jsonRequest('GET', '/api/users?q=' . 'user1');
//        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
//        self::assertCount(1, $items);
//    }
}