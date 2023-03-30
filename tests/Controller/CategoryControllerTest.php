<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    public function testPremiumSuccess()
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/categories');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Toutes vos catégories');
        $this->expectH1('Toutes vos catégories');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', "/categories");
        self::assertResponseRedirects('/connexion');
    }

    public function testIndexAuthenticatedWithoutPremium(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', "/categories");
        self::assertResponseRedirects('/premium');
    }
}
