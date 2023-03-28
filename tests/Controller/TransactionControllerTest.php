<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testIndex(): void
    {
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $this->client->request('GET', '/profil/factures');
        self::assertResponseStatusCodeSame(200);
        $this->expectH1('Mes factures');
        $this->expectTitle('Mes factures');
    }
}
