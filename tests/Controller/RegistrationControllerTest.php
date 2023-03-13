<?php

namespace App\Tests\Controller;

use App\Service\SocialLoginService;
use App\Tests\WebTestCase;
use League\OAuth2\Client\Provider\GithubResourceOwner;

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
        $crawler = $this->client->request('GET', self::SIGNUP_PATH . '?oauth=1');

        // Simulates an oauth session
        $github = new GithubResourceOwner([
            'email' => 'john@doe.fr',
            'login' => 'JohnDoe',
            'id' => 123123,
        ]);
        $loginService = self::getContainer()->get(SocialLoginService::class);
        $this->callMethod($loginService, 'persist', [$github, $this->client->getRequest()]);
        // TODO
//        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
//        $form->setValues([
//            'registration_form' => [
//                'username' => 'Jane Doe',
//            ],
//        ]);
//        $this->client->submit($form);
        $this->expectFormErrors(0);
//        $this->assertResponseRedirects();
        $this->assertEmailCount(0);
    }


}
