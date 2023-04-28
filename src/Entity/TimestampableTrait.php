<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TimestampableTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    private DatetimeImmutable $updatedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    private DatetimeImmutable $createdAt;

    public function getUpdatedAt(): DatetimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DatetimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): DatetimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DatetimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
