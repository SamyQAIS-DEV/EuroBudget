<?php

namespace App\Tests\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InvoiceControllerTest extends WebTestCase
{
    private const CREATION_FORM_BUTTON = 'Créer';
    private const EDITION_FORM_BUTTON = 'Modifier';

    private string $path = '/invoices';

    public function testIndex()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/invoices');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Toutes vos factures');
        $this->expectH1('Toutes vos factures');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', "/invoices");
        self::assertResponseRedirects('/connexion');
    }

    public function testNew(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Invoice::class);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/new', $this->path));
        $form = $crawler->selectButton(self::CREATION_FORM_BUTTON)->form();
        $form->setValues([
            'invoice_form' => [
                'label' => 'Testing',
                'amount' => 10,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/invoices');
        self::assertSame($originalNumObjectsInRepository + 1, $repository->count([]));
    }

    public function testEdit(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Invoice::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $form = $crawler->selectButton(self::EDITION_FORM_BUTTON)->form();
        $form->setValues([
            'invoice_form' => [
                'label' => 'Something New',
                'amount' => 15,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/invoices');
        self::assertSame($originalNumObjectsInRepository, $repository->count([]));
        $fixture = $repository->find($fixture->getId());
        self::assertSame('Something New', $fixture->getLabel());
        self::assertEquals(15, $fixture->getAmount());
    }

    public function testEditAccessDenied(): void
    {
        ['premium_user' => $premiumUser, 'user2' => $user] = $this->loadFixtures(['users']);
        $this->login($premiumUser);
        $repository = $this->em->getRepository(Invoice::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));

        self::assertResponseRedirects('/invoices');
        $this->client->followRedirect();
        $this->expectErrorAlert('Vous ne pouvez pas modifier cette facture.');
    }

    public function testRemove(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Invoice::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', $this->path);
        $form = $crawler->filter('button[title="Supprimer la facture"]')->form();
        $this->client->submit($form);
        self::assertResponseRedirects('/invoices');
        self::assertSame($originalNumObjectsInRepository - 1, $repository->count([]));
    }

    public function testRemoveAccessDenied(): void
    {
        ['premium_user' => $premiumUser, 'user2' => $user] = $this->loadFixtures(['users']);
        $this->login($premiumUser);
        $repository = $this->em->getRepository(Invoice::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);

        $this->client->request('DELETE', sprintf('%s/%s', $this->path, $fixture->getId()));

        self::assertResponseRedirects('/invoices');
        $this->client->followRedirect();
        $this->expectErrorAlert('Vous ne pouvez pas supprimer cette facture.');
    }

    private function getValidEntity(User $user): Invoice
    {
        return (new Invoice())
            ->setLabel('Valid Label')
            ->setAmount(10)
            ->setCreator($user)
            ->setDepositAccount($user->getFavoriteDepositAccount())
            ->setActive(true);
    }
}
