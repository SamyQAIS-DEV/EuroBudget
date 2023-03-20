<?php

namespace App\Tests\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PremiumControllerTest extends WebTestCase
{
    public function testPremium(): void
    {
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/premium');
        self::assertResponseStatusCodeSame(200);
        $this->expectTitle('Devenir premium');
        $this->expectH1('Devenir premium');
    }

    public function testPremiumUnauthenticated(): void
    {
        $this->client->request('GET', "/premium");
        self::assertResponseRedirects('/connexion');
    }
}
