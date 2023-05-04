<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserRequest;
use App\Event\DepositAccountShareRequestCreatedEvent;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\UserRequestRepository;
use DateTimeImmutable;

class UserRequestService
{
    private const HASH_KEY = '::';

    public function __construct(
        private readonly UserRequestRepository $userRequestRepository,
    ) {
    }

    public function create(User $creator, User $target, string $message, object $entity): UserRequest
    {
        $request = (new UserRequest())
            ->setMessage($message)
            ->setCreator($creator)
            ->setTarget($target)
            ->setEntity($this->getHashForEntity($entity));
        $this->userRequestRepository->save($request, true);

        return $request;
    }

    public function getIdFromHash(string $hash): int
    {
        $id = str_replace(self::HASH_KEY, '', strstr($hash, self::HASH_KEY));

        return (int) $id;
    }

    private function getHashForEntity(object $entity): string
    {
        $hash = $entity::class;
        if (method_exists($entity, 'getId')) {
            $hash .= self::HASH_KEY . (string) $entity->getId();
        }

        return $hash;
    }
}
