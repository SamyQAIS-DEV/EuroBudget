<?php

namespace App\Tests\Controller\Admin;

use App\Tests\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testPageAsAdmin(): void
    {
        ['admin_user' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/admin');
        self::assertResponseStatusCodeSame(200);
    }

    public function testPageAsUser(): void
    {
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/admin');
        $this->expectH1('Accès interdit');
        $this->expectBodyContains('Vous n\'avez pas le droit d\'accéder à cette page');
    }

    public function testUnauthenticated(): void
    {
        $this->client->request('GET', "/admin");
        self::assertResponseRedirects('/connexion');
    }
}
