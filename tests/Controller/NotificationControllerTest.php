<?php

namespace App\Tests\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NotificationControllerTest extends WebTestCase
{
    private string $path = '/profil/notifications';

    public function testIndex()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Mes notifications');
        $this->expectH1('Mes notifications');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', $this->path);
        self::assertResponseRedirects('/connexion');
    }
}
