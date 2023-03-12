<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPage(): void
    {
        $crawler = $this->client->request('GET', '/inscription');

        self::assertResponseIsSuccessful();
        $this->expectH1('Register');
    }
}
