<?php

namespace App\Security\Voter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    public const CAN_LOGIN = 'CAN_LOGIN_AS_ADMIN';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $adminIp
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CAN_LOGIN;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::CAN_LOGIN => $this->canLogin(),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canLogin(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {

            return false;
        }

        return $this->adminIp === $request->getClientIp();
    }
}
