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

    public function testSearch(): void
    {
        ['user1' => $user] = $this->data;
        $this->login($user);
        $content = $this->jsonRequest('GET', '/api/users?q=' . $user->getFirstname());
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $items);
        self::assertSame($user->getLastname(), $items[0]->lastname);
        self::assertSame($user->getFirstname(), $items[0]->firstname);
        self::assertSame($user->getEmail(), $items[0]->email);

        $content = $this->jsonRequest('GET', '/api/users?q=' . $user->getLastname());
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $items);
        self::assertSame($user->getLastname(), $items[0]->lastname);
        self::assertSame($user->getFirstname(), $items[0]->firstname);
        self::assertSame($user->getEmail(), $items[0]->email);

        $content = $this->jsonRequest('GET', '/api/users?q=' . $user->getEmail());
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $items);
        self::assertSame($user->getLastname(), $items[0]->lastname);
        self::assertSame($user->getFirstname(), $items[0]->firstname);
        self::assertSame($user->getEmail(), $items[0]->email);

        $content = $this->jsonRequest('GET', '/api/users?q=' . $user->getFullName());
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $items);
        self::assertSame($user->getLastname(), $items[0]->lastname);
        self::assertSame($user->getFirstname(), $items[0]->firstname);
        self::assertSame($user->getEmail(), $items[0]->email);

        $content = $this->jsonRequest('GET', '/api/users?q=Moi');
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $items);
        self::assertSame($user->getLastname(), $items[0]->lastname);
        self::assertSame($user->getFirstname(), $items[0]->firstname);
        self::assertSame($user->getEmail(), $items[0]->email);
    }
}