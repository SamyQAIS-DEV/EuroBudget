<?php

namespace App\Test\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    private const CREATION_FORM_BUTTON = 'Créer';
    private const EDITION_FORM_BUTTON = 'Modifier';

    private string $path = '/categories';

    public function testIndex()
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Toutes vos catégories');
        $this->expectH1('Toutes vos catégories');
    }

    public function testIndexUnauthenticated(): void
    {
        $this->client->request('GET', $this->path);
        self::assertResponseRedirects('/connexion');
    }

    public function testIndexAuthenticatedWithoutPremium(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', $this->path);
        self::assertResponseRedirects('/premium');
    }

    public function testNew(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Category::class);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/new', $this->path));
        $form = $crawler->selectButton(self::CREATION_FORM_BUTTON)->form();
        $form->setValues([
            'category' => [
                'name' => 'Testing',
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/categories');
        self::assertSame($originalNumObjectsInRepository + 1, $repository->count([]));
    }

    public function testEdit(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Category::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));
        $form = $crawler->selectButton(self::EDITION_FORM_BUTTON)->form();
        $form->setValues([
            'category' => [
                'name' => 'Something New',
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/categories');
        self::assertSame($originalNumObjectsInRepository, $repository->count([]));
        $fixture = $repository->find($fixture->getId());
        self::assertSame('Something New', $fixture->getName());
        self::assertSame('something-new', $fixture->getSlug());
    }

    public function testEditAccessDenied(): void
    {
        ['premium_user' => $premiumUser, 'user2' => $user] = $this->loadFixtures(['users']);
        $this->login($premiumUser);
        $repository = $this->em->getRepository(Category::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s/%s/edit', $this->path, $fixture->getId()));

        self::assertResponseRedirects('/categories');
        $this->client->followRedirect();
        $this->expectErrorAlert('Vous ne pouvez pas modifier cette catégorie.');
    }

    public function testRemove(): void
    {
        ['premium_user' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $repository = $this->em->getRepository(Category::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);
        $originalNumObjectsInRepository = $repository->count([]);
        $crawler = $this->client->request('GET', $this->path);
        $form = $crawler->filter('button[title="Supprimer la catégorie"]')->form();
        $this->client->submit($form);
        self::assertResponseRedirects('/categories');
        self::assertSame($originalNumObjectsInRepository - 1, $repository->count([]));
    }

    public function testRemoveAccessDenied(): void
    {
        ['premium_user' => $premiumUser, 'user2' => $user] = $this->loadFixtures(['users']);
        $this->login($premiumUser);
        $repository = $this->em->getRepository(Category::class);
        $fixture = $this->getValidEntity($user);
        $repository->save($fixture, true);

        $this->client->request('DELETE', sprintf('%s/%s', $this->path, $fixture->getId()));

        self::assertResponseRedirects('/categories');
        $this->client->followRedirect();
        $this->expectErrorAlert('Vous ne pouvez pas supprimer cette catégorie.');
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
