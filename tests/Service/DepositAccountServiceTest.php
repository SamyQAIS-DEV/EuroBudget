<?php

namespace App\Tests\Service;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Resource\DepositAccountResource;
use App\Service\DepositAccountService;
use App\Tests\KernelTestCase;
use App\Tests\WebTestCase;

use function PHPUnit\Framework\assertInstanceOf;

class DepositAccountServiceTest extends WebTestCase
{
    private DepositAccountService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(DepositAccountService::class);
    }

    public function testCreate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $depositAccount = $this->service->create((new DepositAccount())->setTitle('Valid Title')->setAmount(10), $user);
        $this->assertInstanceOf(DepositAccount::class, $depositAccount);
        $this->assertSame('Valid Title', $depositAccount->getTitle());
    }

    public function testUpdate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(DepositAccount::class);
        $depositAccount = $this->getValidEntity($user);
        $repository->save($depositAccount, true);
        $depositAccount->setTitle('Updated !!!');
        $depositAccount = $this->service->update($depositAccount);
        $this->assertInstanceOf(DepositAccount::class, $depositAccount);
        $this->assertSame('Updated !!!', $depositAccount->getTitle());
    }

    public function testUpdateFavorite(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(DepositAccount::class);
        $depositAccount = $this->getValidEntity($user);
        $repository->save($depositAccount, true);
        self::assertNotSame($user->getFavoriteDepositAccount()->getId(), $depositAccount->getId());
        $this->service->updateFavorite($depositAccount->getId());
        self::assertSame($user->getFavoriteDepositAccount()->getId(), $depositAccount->getId());
    }

    public function testGetRecap(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $recap = $this->service->getRecap();
        assertInstanceOf(DepositAccountResource::class, $recap);
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