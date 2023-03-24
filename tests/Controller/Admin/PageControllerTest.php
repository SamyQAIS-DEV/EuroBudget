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
//        $this->expectTitle('Devenir premium');
//        $this->expectH1('Devenir premium');
    }

    public function testPageAsUser(): void
    {
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/admin');
        // TODO
//        self::assertResponseRedirects('/error');
//        $this->client->followRedirect();
//        $this->expectErrorAlert('Vous n\'avez pas le droit d\'accéder à cette page');
    }

    public function testUnauthenticated(): void
    {
        $this->client->request('GET', "/admin");
        self::assertResponseRedirects('/connexion');
    }
}
