<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OperationVoter extends Voter
{
    public const POST = 'POST';
    public const UPDATE = 'UPDATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::POST,
            self::UPDATE
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // TODO
        // POST => Count monthly
        // UPDATE => Check ACCESS
        return true;

        return false;
    }
}
