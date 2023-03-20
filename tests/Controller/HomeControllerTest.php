<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $crawler = $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $this->expectH1('Bienvenue sur EuroBudget votre nouvelle plateforme de gestion de comptes en banques');
    }
}
