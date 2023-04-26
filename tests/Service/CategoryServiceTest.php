<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Service\CategoryService;
use App\Tests\KernelTestCase;

class CategoryServiceTest extends KernelTestCase
{
    private CategoryService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(CategoryService::class);
    }

    public function testCreate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $category = $this->service->create((new Category())->setName('Valid Name'), $user);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertSame($user->getFavoriteDepositAccount()->getId(), $category->getDepositAccount()->getId());
    }

    public function testUpdate(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(Category::class);
        $category = $this->getValidEntity($user);
        $repository->save($category, true);
        $category->setName('Updated !!!');
        $category = $this->service->update($category);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertSame('Updated !!!', $category->getName());
    }

    public function testDelete(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $repository = $this->em->getRepository(Category::class);
        $category = $this->getValidEntity($user);
        $repository->save($category, true);
        $this->assertSame(1, $repository->count([]));
        $this->service->delete($category);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertSame(0, $repository->count([]));
    }

    private function getValidEntity(User $user): Category
    {
        return (new Category())
            ->setName('Valid Name')
            ->setSlug('Valid Name')
            ->setOwner($user)
            ->setDepositAccount($user->getFavoriteDepositAccount());
    }
}