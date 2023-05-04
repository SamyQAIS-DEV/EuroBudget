<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserRequestControllerTest extends WebTestCase
{
    private string $path = '/profil/demandes';

    public function testIndex()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Mes demandes');
        $this->expectH1('Mes demandes');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', $this->path);
        self::assertResponseRedirects('/connexion');
    }
}
