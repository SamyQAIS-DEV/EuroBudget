<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testProfile(): void
    {
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/profile');
        self::assertResponseStatusCodeSame(200);
        $this->expectTitle('Mon profil');
        $this->expectH1('Mon profil');
    }

    public function testProfileUnauthenticated(): void
    {
        $this->client->request('GET', "/profile");
        self::assertResponseRedirects('/connexion');
    }
}
