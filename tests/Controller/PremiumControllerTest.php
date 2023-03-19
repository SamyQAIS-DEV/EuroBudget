<?php

namespace App\Tests\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PremiumControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testPremium(): void
    {
        $crawler = $this->client->request('GET', '/premium');
        self::assertResponseStatusCodeSame(200);
        $this->expectTitle('Devenir premium');
        $this->expectH1('Devenir premium');
    }
}
