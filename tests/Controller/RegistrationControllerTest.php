<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SocialLoginService;
use App\Tests\WebTestCase;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    private const SIGNUP_PATH = '/inscription';
    private const PAGE_TITLE = 'Inscription';
    private const TITLE = 'S\'inscrire';
    private const SIGNUP_BUTTON = 'S\'inscrire';

    /** @var User[] */
    private array $users = [];

    private UserRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(UserRepository::class);
    }

    public function testSEO(): void
    {
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertPageTitleContains(self::PAGE_TITLE);
        $this->expectH1(self::TITLE);
    }

    public function testRegisterSendEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(6, $this->repository->count([]));
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'email' => 'jane@doe.fr',
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        self::assertEmailCount(1);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertSame(7, $this->repository->count([]));
//        $this->client->followRedirect();
//        $this->expectAlert('success');
    }

    public function testRegisterExistingEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(6, $this->repository->count([]));
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'email' => strtoupper($this->users['user1']->getEmail()),
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        self::assertEmailCount(0);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(6, $this->repository->count([]));
    }

    public function testWithLongEmail(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(6, $this->repository->count([]));
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'email' => 'fdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktjfdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktj@email.com',
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        self::assertEmailCount(0);
        $this->assertSame(6, $this->repository->count([]));
    }

    public function testRedirectIfLogged(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->login($this->users['user1']);
        $this->client->request('GET', self::SIGNUP_PATH);
        self::assertResponseRedirects('/');
    }

    public function testGithubOauthExistingEmailRegistration(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(6, $this->repository->count([]));
        // Simulates an oauth session
        $this->client->request('GET', self::SIGNUP_PATH);
        $github = new GithubResourceOwner([
            'email' => $this->users['github_user']->getEmail(),
            'login' => 'JohnDoe',
            'id' => $this->users['github_user']->getGithubId(),
        ]);
        $this->client->getContainer()->get(SocialLoginService::class)->persist($this->getSession(), $github);

        $crawler = $this->client->request('GET', self::SIGNUP_PATH.'?oauth=1');
        $this->expectH1('Se connecter avec Github');
        self::assertResponseIsSuccessful();
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        self::assertResponseIsSuccessful();
        self::assertEmailCount(0);
        $this->assertSame(6, $this->repository->count([]));
    }

    public function testGithubOauthRegistration(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->assertSame(6, $this->repository->count([]));
        // Simulates an oauth session
        $this->client->request('GET', self::SIGNUP_PATH);
        $github = new GithubResourceOwner([
            'email' => 'john@doe.fr',
            'login' => 'JohnDoe',
            'id' => 123123,
        ]);
        $this->client->getContainer()->get(SocialLoginService::class)->persist($this->getSession(), $github);

        $crawler = $this->client->request('GET', self::SIGNUP_PATH.'?oauth=1');
        $this->expectH1('Se connecter avec Github');
        self::assertResponseIsSuccessful();
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'agreeTerms' => 1
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        // TODO Checker message
        self::assertResponseRedirects();
        self::assertEmailCount(0);
        $this->assertSame(7, $this->repository->count([]));
    }
}
