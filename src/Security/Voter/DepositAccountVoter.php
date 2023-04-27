<?php

namespace App\Security\Voter;

use App\Entity\DepositAccount;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DepositAccountVoter extends Voter
{
    public const UPDATE = 'UPDATE_DEPOSIT_ACCOUNT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::UPDATE && $subject instanceof DepositAccount;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var DepositAccount $depositAccount */
        $depositAccount = $subject;

        return $depositAccount->getUsers()->contains($user);
    }
}
