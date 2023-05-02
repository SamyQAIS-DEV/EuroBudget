<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait NotifiableTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $notificationsReadAt = null;

    public function getNotificationsReadAt(): ?DateTimeImmutable
    {
        return $this->notificationsReadAt;
    }

    public function setNotificationsReadAt(?DateTimeImmutable $notificationsReadAt): void
    {
        $this->notificationsReadAt = $notificationsReadAt;
    }
}
