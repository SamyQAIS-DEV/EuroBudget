<?php

namespace App\Tests\Service;

use App\Entity\Invoice;
use App\Entity\User;
use App\Service\InvoiceService;
use App\Tests\KernelTestCase;

class InvoiceServiceTest extends KernelTestCase
{
    private InvoiceService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(InvoiceService::class);
    }

    public function testCreate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $invoice = $this->service->create((new Invoice())->setLabel('Valid Label')->setAmount(10), $user);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame($user->getFavoriteDepositAccount()->getId(), $invoice->getDepositAccount()->getId());
    }

    public function testUpdate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(Invoice::class);
        $invoice = $this->getValidEntity($user);
        $repository->save($invoice, true);
        $invoice->setLabel('Updated !!!');
        $invoice = $this->service->update($invoice);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame('Updated !!!', $invoice->getLabel());
    }

    public function testDelete(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(Invoice::class);
        $invoice = $this->getValidEntity($user);
        $repository->save($invoice, true);
        $this->assertSame(1, $repository->count([]));
        $this->service->delete($invoice);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame(0, $repository->count([]));
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