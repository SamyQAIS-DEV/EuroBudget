<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private const SIGNIN_PATH = '/connexion';
    private const PAGE_TITLE = 'Connexion';
    private const TITLE = 'Se connecter';
    private const SIGNIN_BUTTON = 'Se connecter';

    /** @var User[] */
    private array $users = [];

    public function testSEO(): void
    {
        $crawler = $this->client->request('GET', self::SIGNIN_PATH);
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
        // TODO Checker message
        //        $this->expectAlert('success');
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

    // TODO
//    public function testConfirmationTokenInvalid(): void
//    {
//        /** @var User[] $users */
//        $users = $this->loadFixtures(['users']);
//        $user = $users['user_unconfirmed'];
//
//        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token=azeazeaze');
//        $this->assertResponseRedirects(self::SIGNIN_PATH);
//        $this->client->followRedirect();
//        $this->expectErrorAlert();
//    }

    // TODO
//    public function testConfirmationTokenValid(): void
//    {
//        /** @var User[] $users */
//        $users = $this->loadFixtures(['users']);
//        $user = $users['user_unconfirmed'];
//        $user->setCreatedAt(new \DateTime());
//        $this->em->flush();
//
//        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token='.$user->getConfirmationToken());
//        $this->assertResponseRedirects();
//        $this->client->followRedirect();
//        $this->expectSuccessAlert();
//    }

    // TODO
//    public function testConfirmationTokenExpire(): void
//    {
//        /** @var User[] $users */
//        $users = $this->loadFixtures(['users']);
//        $user = $users['user_unconfirmed'];
//        $user->setCreatedAt(new \DateTime('-1 day'));
//        $this->em->flush();
//
//        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token='.$user->getConfirmationToken());
//        $this->assertResponseRedirects(self::SIGNIN_PATH);
//        $this->client->followRedirect();
//        $this->expectErrorAlert();
//    }

    public function testRedirectIfLogged(): void
    {
        $this->users = $this->loadFixtureFiles(['users']);
        $this->login($this->users['user1']);
        $this->client->request('GET', self::SIGNIN_PATH);
        self::assertResponseRedirects('/');
    }
}
