<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait InactivityRemindableTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private DateTimeImmutable $lastLoginAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $activityReminderSentAt = null;

    public function getLastLoginAt(): DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(DateTimeImmutable $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getActivityReminderSentAt(): ?DateTimeImmutable
    {
        return $this->activityReminderSentAt;
    }

    public function setActivityReminderSentAt(?DateTimeImmutable $activityReminderSentAt): void
    {
        $this->activityReminderSentAt = $activityReminderSentAt;
    }
}
