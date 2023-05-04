<?php

namespace App\Security\Voter;

use App\Entity\UserRequest;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserRequestVoter extends Voter
{
    public const ANSWER = 'ANSWER_USER_REQUEST';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ANSWER && $subject instanceof UserRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var UserRequest $request */
        $request = $subject;

        return match ($attribute) {
            self::ANSWER => !$request->isAnswered(),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
