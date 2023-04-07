<?php

namespace App\Security\Voter;

use App\Entity\Operation;
use App\Entity\User;
use App\Repository\OperationRepository;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OperationVoter extends Voter
{
    public const MONTHLY_QUOTA = 15;

    public const POST = 'POST_OPERATION';
    public const UPDATE = 'UPDATE_OPERATION';

    public function __construct(
        private readonly OperationRepository $operationRepository
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::POST || ($attribute === self::UPDATE && $subject instanceof Operation);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Operation $operation */
        $operation = $subject;

        return match($attribute) {
            self::POST => $this->canPost($user),
            self::UPDATE => $this->canUpdate($operation, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canPost(User $user): bool
    {
        if ($user->isPremium()) {
            return true;
        }
        return $this->canCreateThisMonth($user);
    }

    private function canUpdate(Operation $operation, User $user): bool
    {
        return $operation->getDepositAccount()->getUsers()->contains($user);
    }

    private function canCreateThisMonth(User $user): bool
    {
        $now = new DateTime();
        $count = $this->operationRepository->countForYearAndMonth($user->getFavoriteDepositAccount()->getId(), (int) $now->format('Y'), (int) $now->format('m'));

        return $count <= self::MONTHLY_QUOTA;
    }
}
