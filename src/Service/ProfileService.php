<?php

namespace App\Service;

use App\Dto\AvatarDto;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileService
{
    private const SIZE = 150;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function updateAvatar(AvatarDto $data): void
    {
        if (false === $data->file->getRealPath()) {
            throw new RuntimeException('Impossible de redimensionner un avatar non existant');
        }

        $manager = new ImageManager(['driver' => 'gd']);
        $manager->make($data->file)->fit(self::SIZE, self::SIZE)->save($data->file->getRealPath());

        $data->user->setAvatarFile($data->file);
        $data->user->setUpdatedAt(new DateTimeImmutable());

        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            throw new RuntimeException($errors->get(0)->getMessage());
        }

        $this->entityManager->flush();
    }
}