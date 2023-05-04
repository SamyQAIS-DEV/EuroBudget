<?php

namespace App\Tests\Controller;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Tests\WebTestCase;

class DepositAccountControllerTest extends WebTestCase
{
    private const CREATION_FORM_BUTTON = 'Créer';
    private const EDITION_FORM_BUTTON = 'Modifier';
    private const SHARE_FORM_BUTTON = 'Partager';

    private string $path = '/deposit-accounts';

    public function testNewUnauthenticated(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));
        self::assertResponseRedirects('/connexion');
    }

    public function testNew(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(DepositAccount::class);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/new', $this->path));
        $form = $crawler->selectButton(self::CREATION_FORM_BUTTON)->form();
        $form->setValues([
            'deposit_account_form' => [
                'title' => 'Testing',
                'amount' => 10,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/');
        self::assertSame($originalNumObjectsInRepository + 1, $repository->count([]));
    }

    public function testEdit(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(DepositAccount::class);
        $depositAccount = $user->getFavoriteDepositAccount();
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $depositAccount->getId()));
        $form = $crawler->selectButton(self::EDITION_FORM_BUTTON)->form();
        $form->setValues([
            'deposit_account_form' => [
                'title' => 'Something New',
                'amount' => 15,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/');
        self::assertSame($originalNumObjectsInRepository, $repository->count([]));
        $fixture = $repository->find($depositAccount->getId());
        self::assertSame('Something New', $fixture->getTitle());
        self::assertEquals(15, $fixture->getAmount());
    }

    public function testEditAccessDenied(): void
    {
        ['user1' => $premiumUser, 'user2' => $user] = $this->loadFixtures(['users']);
        $this->login($premiumUser);
        $repository = $this->em->getRepository(DepositAccount::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);
        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        self::assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->expectErrorAlert('Vous ne pouvez pas modifier ce compte en banque.');
    }

    public function testShare(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);
        $this->login($user1);
        $crawler = $this->client->request('GET', sprintf('%s/%d/partager', $this->path, $user2->getId()));
        $this->expectH1('Partager un compte en banque');
        $this->expectH2(sprintf('Avec : %s - %s', $user2->getFullName(), $user2->getEmail()));
        $form = $crawler->selectButton(self::SHARE_FORM_BUTTON)->form();
        $form->setValues([
            'deposit_account_share_request_form' => [
                'depositAccount' => $user1->getFavoriteDepositAccount()->getId(),
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects(sprintf('/profil/%d', $user2->getId()));
    }

    private function getValidEntity(User $user): DepositAccount
    {
        return (new DepositAccount())
            ->setTitle('Valid Title')
            ->setAmount(10)
            ->setCreator($user);
    }
}
