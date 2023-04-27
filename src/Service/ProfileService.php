<?php

namespace App\Service;

use App\Dto\AvatarDto;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileService
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function updateAvatar(AvatarDto $data): void
    {
        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            throw new RuntimeException($errors->get(0)->getMessage());
        }
        if (false === $data->file->getRealPath()) {
            throw new RuntimeException('Impossible de redimensionner un avatar non existant');
        }

        $data->user->setAvatarFile($data->file);
        $data->user->setUpdatedAt(new DateTimeImmutable());
        $this->entityManager->flush();
    }
}