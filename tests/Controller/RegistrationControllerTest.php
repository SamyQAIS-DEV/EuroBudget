<?php

namespace App\Tests\Controller;

use App\Service\SocialLoginService;
use App\Tests\WebTestCase;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class RegistrationControllerTest extends WebTestCase
{
    private const SIGNUP_PATH = '/inscription';
    private const CONFIRMATION_PATH = '/inscription/confirmation/';
    private const SIGNUP_BUTTON = "S'inscrire";

    public function testRegistrationPage(): void
    {
        $crawler = $this->client->request('GET', '/inscription');

        self::assertResponseIsSuccessful();
        $this->expectH1('Register');
    }

    public function testOauthRegistration(): void
    {
        // Simulates an oauth session
        $this->client->request('GET', self::SIGNUP_PATH);
        $github = new GithubResourceOwner([
            'email' => 'john@doe.fr',
            'login' => 'JohnDoe',
            'id' => 123123,
        ]);
        $this->client->getContainer()->get(SocialLoginService::class)->persist($this->getSession(), $github);

        $crawler = $this->client->request('GET', self::SIGNUP_PATH . '?oauth=1');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'email' => 'john@doe.fr',
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
//        self::assertResponseIsSuccessful();
        self::assertResponseRedirects();
        self::assertEmailCount(0);
    }
}
