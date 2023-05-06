<?php

namespace App\Tests\Controller;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Entity\User;
use App\Tests\WebTestCase;

class TransferControllerTest extends WebTestCase
{
    private const CREATION_FORM_BUTTON = 'Créer';

    private string $path = '/transfers';

    public function testNewUnauthenticated(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));
        self::assertResponseRedirects('/connexion');
    }

    public function testNew(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $depositAccountRepository = $this->em->getRepository(DepositAccount::class);
        $fixture = $this->getValidEntity($user);
        $depositAccountRepository->save($fixture, true);

        $operationRepository = $this->em->getRepository(Operation::class);
        $originalNumObjectsInRepository = $operationRepository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/new', $this->path));
        $form = $crawler->selectButton(self::CREATION_FORM_BUTTON)->form();
        $form->setValues([
            'transfer_form' => [
                'fromDepositAccount' => $user->getFavoriteDepositAccount()->getId(),
                'targetDepositAccount' => $fixture->getId(),
                'amount' => 10,
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        self::assertResponseRedirects('/');
        self::assertSame($originalNumObjectsInRepository + 2, $operationRepository->count([]));
    }

    public function testNewSameDepositAccounts(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $operationRepository = $this->em->getRepository(Operation::class);
        $originalNumObjectsInRepository = $operationRepository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/new', $this->path));
        $form = $crawler->selectButton(self::CREATION_FORM_BUTTON)->form();
        $form->setValues([
            'transfer_form' => [
                'fromDepositAccount' => $user->getFavoriteDepositAccount()->getId(),
                'targetDepositAccount' => $user->getFavoriteDepositAccount()->getId(),
                'amount' => 10,
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(2);
        self::assertSame($originalNumObjectsInRepository, $operationRepository->count([]));
    }

    public function testNewAccessDeniedFromDepositAccount(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);
        $this->login($user1);

        $operationRepository = $this->em->getRepository(Operation::class);
        $originalNumObjectsInRepository = $operationRepository->count([]);
        $data = ['fromDepositAccount' => json_encode($user1->getFavoriteDepositAccount()), 'targetDepositAccount' => json_encode($user2->getFavoriteDepositAccount()), 'amount' => 10];
        $this->client->request('POST', sprintf('%s/new', $this->path), $data);
        self::assertSame($originalNumObjectsInRepository, $operationRepository->count([]));
    }

    public function testNewAccessDeniedTargetDepositAccount(): void
    {
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users']);
        $this->login($user1);

        $operationRepository = $this->em->getRepository(Operation::class);
        $originalNumObjectsInRepository = $operationRepository->count([]);
        $data = ['fromDepositAccount' => json_encode($user2->getFavoriteDepositAccount()), 'targetDepositAccount' => json_encode($user1->getFavoriteDepositAccount()), 'amount' => 10];
        $this->client->request('POST', sprintf('%s/new', $this->path), $data);
        self::assertSame($originalNumObjectsInRepository, $operationRepository->count([]));
    }

    private function getValidEntity(User $user): DepositAccount
    {
        return (new DepositAccount())
            ->setTitle('Valid Title')
            ->setAmount(10)
            ->setCreator($user)
            ->addUser($user);
    }
}
