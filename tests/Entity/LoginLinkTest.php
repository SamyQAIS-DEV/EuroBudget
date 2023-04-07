<?php

namespace App\Tests\Entity;

use App\Entity\LoginLink;
use App\Helper\TimeHelper;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class LoginLinkTest extends TestCase
{
    public function testIsExpired(): void
    {
        $this->assertTrue((new LoginLink())->setCreatedAt(new DateTimeImmutable('-40 minutes'))->isExpired());
        $this->assertFalse((new LoginLink())->setCreatedAt(new DateTimeImmutable('-10 minutes'))->isExpired());
    }

    public function testExpiresAt(): void
    {
        $this->assertSame((new DateTime('+20 minutes'))->format('Y-m-d H:i'), (new LoginLink())->setCreatedAt(new DateTimeImmutable('-10 minutes'))->getExpiresAt()->format('Y-m-d H:i'));
        $this->assertSame((new DateTime('+30 minutes'))->format('Y-m-d H:i'), (new LoginLink())->setCreatedAt(new DateTimeImmutable())->getExpiresAt()->format('Y-m-d H:i'));
    }

    public function testLeftTime(): void
    {
        $this->assertSame('20 min', TimeHelper::leftTime((new LoginLink())->setCreatedAt(new DateTimeImmutable('-10 minutes'))->getExpiresAt()));
        $this->assertSame('30 min', TimeHelper::leftTime((new LoginLink())->setCreatedAt(new DateTimeImmutable())->getExpiresAt()));
    }
}
