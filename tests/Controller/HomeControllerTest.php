<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $crawler = $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $this->expectH1('EuroBudget est une plateforme de gestion de comptes en banques');
    }
}
