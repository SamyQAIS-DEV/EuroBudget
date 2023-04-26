<?php

namespace App\Tests\Security\Voter;

use App\Entity\User;
use App\Security\Voter\InvoiceVoter;
use App\Tests\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InvoiceVoterTest extends KernelTestCase
{
    private TokenInterface $token;

    private InvoiceVoter $voter;

    public function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->voter = new InvoiceVoter();
    }

    private function getUser(): User
    {
        return (new User())->setId(0)->setEmail('johndoe@domain.fr');
    }
}
