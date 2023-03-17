<?php

namespace App\Tests\Entity;

use App\Entity\LoginLinkToken;
use App\Helper\TimeHelper;
use DateTime;
use PHPUnit\Framework\TestCase;

class LoginLinkTokenTest extends TestCase
{
    public function testIsExpired(): void
    {
        $this->assertTrue((new LoginLinkToken())->setCreatedAt(new DateTime('-40 minutes'))->isExpired());
        $this->assertFalse((new LoginLinkToken())->setCreatedAt(new DateTime('-10 minutes'))->isExpired());
    }

    public function testExpiresAt(): void
    {
        $this->assertSame((new DateTime('+20 minutes'))->format('Y-m-d H:i'), (new LoginLinkToken())->setCreatedAt(new DateTime('-10 minutes'))->getExpiresAt()->format('Y-m-d H:i'));
        $this->assertSame((new DateTime('+30 minutes'))->format('Y-m-d H:i'), (new LoginLinkToken())->setCreatedAt(new DateTime())->getExpiresAt()->format('Y-m-d H:i'));
    }

    public function testLeftTime(): void
    {
        $this->assertSame('20 min', TimeHelper::leftTime((new LoginLinkToken())->setCreatedAt(new DateTime('-10 minutes'))->getExpiresAt()));
        $this->assertSame('30 min', TimeHelper::leftTime((new LoginLinkToken())->setCreatedAt(new DateTime())->getExpiresAt()));
    }
}
