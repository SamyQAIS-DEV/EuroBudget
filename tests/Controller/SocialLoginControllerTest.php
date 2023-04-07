<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class SocialLoginControllerTest extends WebTestCase
{
    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->users = $this->loadFixtures(['users']);
    }

    public function testConnectGithub(): void
    {
        $crawler = $this->client->request('GET', '/oauth/connect/github');

        self::assertResponseRedirects();
    }

    public function testDisconnectGithub(): void
    {
        $this->login($this->users['user1']);
        $crawler = $this->client->request('GET', '/oauth/unlink/github');

        self::assertResponseRedirects();
    }
}
