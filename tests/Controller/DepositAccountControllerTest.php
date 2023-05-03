<?php

namespace App\Tests\Controller;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Tests\WebTestCase;

class DepositAccountControllerTest extends WebTestCase
{
    private const CREATION_FORM_BUTTON = 'Créer';
    private const EDITION_FORM_BUTTON = 'Modifier';

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

    // TODO
    public function testShare(): void
    {

    }

    private function getValidEntity(User $user): DepositAccount
    {
        return (new DepositAccount())
            ->setTitle('Valid Title')
            ->setAmount(10)
            ->setCreator($user);
    }
}
