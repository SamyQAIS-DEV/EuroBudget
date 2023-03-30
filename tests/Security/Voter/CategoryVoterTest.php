<?php

namespace App\Tests\Security\Voter;

use App\Entity\LoginLink;
use App\Entity\User;
use App\Security\Voter\CategoryVoter;
use App\Service\LoginLinkService;
use App\Tests\KernelTestCase;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CategoryVoterTest extends KernelTestCase
{
    private TokenInterface $token;

    private CategoryVoter $voter;

    public function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->voter = new CategoryVoter();
    }
    public function testAccessPremium(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser()->setPremiumEnd(new DateTimeImmutable('+1 day')));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, null, [CategoryVoter::ACCESS]));
    }

    public function testAccessWithoutPremium(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser());
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, null, [CategoryVoter::ACCESS]));
    }

    private function getUser(): User
    {
        return (new User())->setId(0)->setEmail('johndoe@domain.fr');
    }
}
