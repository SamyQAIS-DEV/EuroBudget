<?php

namespace App\Test\Controller;

use App\Entity\Invoice;
use App\Entity\Operation;
use App\Entity\User;
use App\Tests\WebTestCase;

class OperationControllerTest extends WebTestCase
{
    private string $path = '/operations';

    public function testCreateFromInvoices(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Operation::class);
        $this->em->getRepository(Invoice::class)->save($this->getValidEntity($user), true);
        $originalNumObjectsInRepository = $repository->count([]);
        $this->client->request('POST', sprintf('%s/create-from-invoices', $this->path));
        self::assertResponseRedirects('/');
        self::assertSame($originalNumObjectsInRepository + 1, $repository->count([]));
    }

    private function getValidEntity(User $user): Invoice
    {
        return (new Invoice())
            ->setLabel('Valid label')
            ->setAmount(10)
            ->setCreator($user)
            ->setDepositAccount($user->getFavoriteDepositAccount());
    }
}
