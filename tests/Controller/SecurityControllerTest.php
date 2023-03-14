<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityControllerTest extends WebTestCase
{
    private const SIGNIN_PATH = '/connexion';
    private const LOGIN_CHECK_PATH = '/login-link-check';
    private const PAGE_TITLE = 'Connexion';
    private const TITLE = 'Se connecter';
    private const SIGNIN_BUTTON = 'Se connecter';

    /** @var User[] */
    private array $users = [];

    public function testSEO(): void
    {
        $this->client->request('GET', self::SIGNIN_PATH);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertPageTitleContains(self::PAGE_TITLE);
        $this->expectH1(self::TITLE);
    }

    public function testLoginExistingEmailSendMail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $crawler = $this->client->request('GET', self::SIGNIN_PATH);
        $form = $crawler->selectButton(self::SIGNIN_BUTTON)->form();
        $form->setValues([
            'login_form' => [
                'email' => $this->users['user1']->getEmail(),
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        self::assertEmailCount(1);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->expectSuccessAlert('Login link sent');
    }

    public function testLoginNotExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $crawler = $this->client->request('GET', self::SIGNIN_PATH);
        $form = $crawler->selectButton(self::SIGNIN_BUTTON)->form();
        $form->setValues([
            'login_form' => [
                'email' => 'jane@doe.fr',
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        self::assertEmailCount(0);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testWithLongEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $crawler = $this->client->request('GET', self::SIGNIN_PATH);
        $form = $crawler->selectButton(self::SIGNIN_BUTTON)->form();
        $form->setValues([
            'login_form' => [
                'email' => 'fdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktjfdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktj@email.com',
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        self::assertEmailCount(0);
    }

    public function testConfirmationTokenInvalid(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $user = $this->users['user1'];
        $this->client->request('GET', self::LOGIN_CHECK_PATH . '?user=' . $user->getEmail() . '&expires=11111&hash=wronghash');
        self::assertResponseRedirects(self::SIGNIN_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testConfirmationTokenValid(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $user = $this->users['user1'];
        $this->client->request('GET', self::SIGNIN_PATH);

        $loginLinkHandler = self::getContainer()->get(LoginLinkHandlerInterface::class);
        $loginLink = $loginLinkHandler->createLoginLink($user, $this->getRequest());

        $this->client->request('GET', $loginLink->getUrl());
        self::assertResponseRedirects('/');
    }

    public function testRedirectIfLogged(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->login($this->users['user1']);
        $this->client->request('GET', self::SIGNIN_PATH);
        self::assertResponseRedirects('/');
    }
}
