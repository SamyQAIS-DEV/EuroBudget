<?php

namespace App\Tests\Controller\Api;

use App\Entity\Operation;
use App\Entity\User;
use App\Security\Voter\OperationVoter;
use App\Tests\WebTestCase;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OperationControllerTest extends WebTestCase
{
    public array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'operations']);
    }

    public function testYearsMonths(): void
    {
        ['user1' => $user] = $this->data;
        $this->login($user);
        $now = new DateTime();
        $content = $this->jsonRequest('GET', '/api/operations/years-months');
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(2, $items);
        $this->assertSame($now->format('Y/m'), $items[0]->path);
        $this->assertSame(1, $items[0]->count);
    }

    public function testYearsMonthsWithoutOperations(): void
    {
        ['admin_user' => $user] = $this->data;
        $this->login($user);
        $now = new DateTime();
        $content = $this->jsonRequest('GET', '/api/operations/years-months');
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(1, $items);
        $this->assertSame($now->format('Y/m'), $items[0]->path);
        $this->assertSame(0, $items[0]->count);
    }

    public function testCurrentMonth(): void
    {
        ['user1' => $user] = $this->data;
        $this->login($user);
        $content = $this->jsonRequest('GET', '/api/operations/current-month');
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(1, $items);
    }

    public function testForMonth(): void
    {
        ['user1' => $user] = $this->data;
        $this->login($user);
        $now = new DateTime();
        $content = $this->jsonRequest('GET', '/api/operations/' . $now->format('Y/m'));
        $items = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(1, $items);
    }

    public function testPost(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->data;
        $this->login($user);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $operation = (new Operation())->setLabel('Label')->setAmount(15)->setType('-')->setDate(new DateTimeImmutable())->setPast(false);
        $content = $this->jsonRequest(Request::METHOD_POST, '/api/operations', $operation);
        $item = $this->serializer->deserialize($content, Operation::class, 'json');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(11, $this->em->getRepository(Operation::class)->count([]));
        $this->assertSame('Label', $item->getLabel());
        $this->assertSame(15.0, $item->getAmount());
    }

    public function testPostExceedsMonthlyQuota(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->data;
        $this->login($user);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $operation = (new Operation())->setLabel('Label')->setAmount(15)->setType('-')->setDate(new DateTimeImmutable())->setPast(false);
        for ($i = 0; $i < OperationVoter::MONTHLY_QUOTA; $i++) {
            $this->jsonRequest(Request::METHOD_POST, '/api/operations', $operation);
        }
        $this->assertSame(25, $this->em->getRepository(Operation::class)->count([]));
        $this->jsonRequest(Request::METHOD_POST, '/api/operations', $operation);
        self::assertResponseStatusCodeSame(Response::HTTP_MOVED_PERMANENTLY);
        $this->assertSame(25, $this->em->getRepository(Operation::class)->count([]));
    }

    public function testPostExceedsMonthlyQuotaPremium(): void
    {
        /** @var User $user */
        ['premium_user' => $user] = $this->data;
        $this->login($user);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $operation = (new Operation())->setLabel('Label')->setAmount(15)->setType('-')->setDate(new DateTimeImmutable())->setPast(false);
        for ($i = 0; $i < OperationVoter::MONTHLY_QUOTA; $i++) {
            $this->jsonRequest(Request::METHOD_POST, '/api/operations', $operation);
        }
        $this->assertSame(25, $this->em->getRepository(Operation::class)->count([]));
        $content = $this->jsonRequest(Request::METHOD_POST, '/api/operations', $operation);
        $item = $this->serializer->deserialize($content, Operation::class, 'json');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(26, $this->em->getRepository(Operation::class)->count([]));
        $this->assertSame('Label', $item->getLabel());
        $this->assertSame(15.0, $item->getAmount());
    }

    public function testPut(): void
    {
        /** @var User $user
         * @var Operation $operation */
        ['user1' => $user, 'operation1' => $operation] = $this->data;
        $this->login($user);
        $operation->setLabel('Label Modifié');
        $content = $this->jsonRequest(Request::METHOD_PUT, '/api/operations/' . $operation->getId(), $operation);
        $item = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('Label Modifié', $item->label);
    }

    public function testPutAccessDenied(): void
    {
        /** @var User $user
         * @var Operation $operation */
        ['user1' => $user, 'operation2' => $operation] = $this->data;
        $this->login($user);
        $operation->setLabel('Label Modifié');
        $content = $this->jsonRequest(Request::METHOD_DELETE, '/api/operations/' . $operation->getId());
        $item = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSame('Vous ne pouvez pas supprimer cette opération.', $item->title);
    }

    public function testDelete(): void
    {
        /** @var User $user
         * @var Operation $operation */
        ['user1' => $user, 'operation1' => $operation] = $this->data;
        $this->login($user);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $content = $this->jsonRequest(Request::METHOD_DELETE, '/api/operations/' . $operation->getId());
        $item = json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(9, $this->em->getRepository(Operation::class)->count([]));
        $this->assertSame(null, $item);
    }

    public function testDeleteAccessDenied(): void
    {
        /** @var User $user
         * @var Operation $operation */
        ['user1' => $user, 'operation2' => $operation] = $this->data;
        $this->login($user);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
        $content = $this->jsonRequest(Request::METHOD_DELETE, '/api/operations/' . $operation->getId());
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertSame(10, $this->em->getRepository(Operation::class)->count([]));
    }
}