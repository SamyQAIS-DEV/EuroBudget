<?php

namespace App\Tests\Security\Voter;

use App\Entity\DepositAccount;
use App\Entity\Operation;
use App\Entity\User;
use App\Repository\OperationRepository;
use App\Security\Voter\OperationVoter;
use App\Tests\KernelTestCase;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OperationVoterTest extends KernelTestCase
{
    private OperationRepository $operationRepository;

    private TokenInterface $token;

    private OperationVoter $voter;

    public function setUp(): void
    {
        $this->operationRepository = $this->createMock(OperationRepository::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->voter = new OperationVoter($this->operationRepository);
    }

    public function testPost(): void
    {
        $this->operationRepository->method('countForYearAndMonth')->willReturn(OperationVoter::MONTHLY_QUOTA - 1);
        $this->token->method('getUser')->willReturn($this->getUser());
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, null, [OperationVoter::POST]));
    }

    public function testPostExceedsMonthlyQuota(): void
    {
        $this->operationRepository->method('countForYearAndMonth')->willReturn(OperationVoter::MONTHLY_QUOTA + 1);
        $this->token->method('getUser')->willReturn($this->getUser());
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, null, [OperationVoter::POST]));
    }

    public function testPostExceedsMonthlyQuotaPremium(): void
    {
        $this->operationRepository->method('countForYearAndMonth')->willReturn(OperationVoter::MONTHLY_QUOTA + 1);
        $this->token->method('getUser')->willReturn($this->getUser()->setPremiumEnd(new DateTimeImmutable('+1 day')));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, null, [OperationVoter::POST]));
    }

    public function testUpdate(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser());
        $users = $this->createMock(Collection::class);
        $users->method('contains')->willReturn(true);
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getUsers')->willReturn($users);
        $operation = $this->createMock(Operation::class);
        $operation->method('getDepositAccount')->willReturn($depositAccount);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, $operation, [OperationVoter::UPDATE]));
    }

    public function testUpdateWithoutDepositAccountAccess(): void
    {
        $this->token->method('getUser')->willReturn($this->getUser());
        $users = $this->createMock(Collection::class);
        $users->method('contains')->willReturn(false);
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getUsers')->willReturn($users);
        $operation = $this->createMock(Operation::class);
        $operation->method('getDepositAccount')->willReturn($depositAccount);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, $operation, [OperationVoter::UPDATE]));
    }

    public function testCanCreateFromInvoices(): void
    {
        $this->operationRepository->method('countFromInvoicesForYearAndMonth')->willReturn(0);
        $this->token->method('getUser')->willReturn($this->getUser());
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, null, [OperationVoter::CAN_CREATE_FROM_INVOICES]));
    }

    public function testCanCreateFromInvoicesDenied(): void
    {
        $this->operationRepository->method('countFromInvoicesForYearAndMonth')->willReturn(1);
        $this->token->method('getUser')->willReturn($this->getUser());
        $this->assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token, null, [OperationVoter::CAN_CREATE_FROM_INVOICES]));
    }

    private function getUser(): User
    {
        $depositAccount = $this->createMock(DepositAccount::class);
        $depositAccount->method('getId')->willReturn(0);
        return (new User())->setId(0)->setEmail('johndoe@domain.fr')->setFavoriteDepositAccount($depositAccount);
    }
}
