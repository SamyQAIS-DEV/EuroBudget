<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InvoiceControllerTest extends WebTestCase
{
    public function testSuccess()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/invoices');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Toutes vos factures');
        $this->expectH1('Toutes vos factures');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', "/invoices");
        self::assertResponseRedirects('/connexion');
    }
}
