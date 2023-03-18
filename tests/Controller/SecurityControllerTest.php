<?php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use App\Entity\LoginLink;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private const PAGE_TITLE = 'Connexion';
    private const TITLE = 'Se connecter';
    private const SIGNIN_BUTTON = 'Se connecter';

    private Router $router;

    /** @var User[] */
    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->router = $this->client->getContainer()->get('router');
    }

    public function testSEO(): void
    {
        $this->client->request('GET', $this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertPageTitleContains(self::PAGE_TITLE);
        $this->expectH1(self::TITLE);
    }

    public function testLoginExistingEmailSendMail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $crawler = $this->client->request('GET', $this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
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
        $crawler = $this->client->request('GET', $this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
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
        $crawler = $this->client->request('GET', $this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
        $form = $crawler->selectButton(self::SIGNIN_BUTTON)->form();
        $form->setValues([
            'login_form' => [
                'email' => 'fdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktjfdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktj@domain.fr',
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        self::assertEmailCount(0);
    }

    public function testNotFoundLoginLink(): void
    {
        $route = $this->router->generate(SecurityController::CHECK_ROUTE_NAME, ['token' => 'coucou']);
        $this->client->request('GET', $route);
        self::assertResponseRedirects($this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
        $this->client->followRedirect();
        $this->expectErrorAlert('Token Expired');
    }

    public function testExpiredLoginLink(): void
    {
        /** @var LoginLink $loginLink */
        ['user2_login_link' => $loginLink] = $this->loadFixtureFiles(['login-links']);
        $route = $this->router->generate(SecurityController::CHECK_ROUTE_NAME, ['token' => $loginLink->getToken()]);
        $this->client->request('GET', $route);
        self::assertResponseRedirects($this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
        $this->client->followRedirect();
        $this->expectErrorAlert('Token Expired');
    }

    public function testConfirmationTokenValid(): void
    {
        /** @var LoginLink $loginLink */
        ['user1_login_link' => $loginLink] = $this->loadFixtureFiles(['login-links']);
        $route = $this->router->generate(SecurityController::CHECK_ROUTE_NAME, ['token' => $loginLink->getToken()]);
        $this->client->request('GET', $route);
        self::assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->expectSuccessAlert('Connecté');
    }

    public function testRedirectIfLogged(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->login($this->users['user1']);
        $this->client->request('GET', $this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
        self::assertResponseRedirects('/');
    }
}
