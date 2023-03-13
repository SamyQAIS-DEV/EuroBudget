<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $crawler = $this->client->request('GET', '/connexion');

        self::assertResponseIsSuccessful();
        $this->expectH1('Connexion');
    }
}
