<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AvatarDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], minWidth: 100, maxWidth: 1400, maxHeight: 1400, minHeight: 100)]
        public UploadedFile $file,
        public User $user
    ) {
    }
}