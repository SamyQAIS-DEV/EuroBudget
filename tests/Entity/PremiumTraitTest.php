<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\Domain\Premium\Entity\PremiumTraitUser;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PremiumTraitTest extends TestCase
{
    public function testNotPremium()
    {
        $user = new User();
        $this->assertFalse($user->isPremium());
    }

    public function testPremiumExpired()
    {
        $user = new User();
        $user->setPremiumEnd(new DateTimeImmutable('-1 year'));
        $this->assertFalse($user->isPremium());
    }

    public function testPremium()
    {
        $user = new User();
        $user->setPremiumEnd(new DateTimeImmutable('+1 year'));
        $this->assertTrue($user->isPremium());
    }
}
