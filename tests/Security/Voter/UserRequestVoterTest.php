<?php

namespace App\Tests\Security\Voter;

use App\Entity\UserRequest;
use App\Security\Voter\UserRequestVoter;
use App\Tests\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserRequestVoterTest extends KernelTestCase
{
    private TokenInterface $token;

    private UserRequestVoter $voter;

    public function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->voter = new UserRequestVoter();
    }
    public function testAnswerAnswered(): void
    {
        $request = (new UserRequest())->setAccepted(true);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, $request, [UserRequestVoter::ANSWER]));
    }

    public function testAnswerUnanswered(): void
    {
        $request = new UserRequest();
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, $request, [UserRequestVoter::ANSWER]));
    }
}
