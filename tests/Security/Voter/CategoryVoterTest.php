<?php

namespace App\Tests\Security\Voter;

use App\Entity\Category;
use App\Entity\DepositAccount;
use App\Entity\User;
use App\Security\Voter\CategoryVoter;
use App\Tests\KernelTestCase;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
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

    public function testUpdate(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser());
        $users = $this->createMock(Collection::class);
        $users->method('contains')->willReturn(true);
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getUsers')->willReturn($users);
        $category = $this->createMock(Category::class);
        $category->method('getDepositAccount')->willReturn($depositAccount);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, $category, [CategoryVoter::UPDATE]));
    }

    public function testUpdateWithoutDepositAccountAccess(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser());
        $users = $this->createMock(Collection::class);
        $users->method('contains')->willReturn(false);
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getUsers')->willReturn($users);
        $category = $this->createMock(Category::class);
        $category->method('getDepositAccount')->willReturn($depositAccount);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, $category, [CategoryVoter::UPDATE]));
    }

    private function getUser(): User
    {
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getId')->willReturn(0);
        return (new User())->setId(0)->setEmail('johndoe@domain.fr')->setFavoriteDepositAccount($depositAccount);
    }
}
