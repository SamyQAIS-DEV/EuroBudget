<?php

namespace App\Security\Voter;

use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InvoiceVoter extends Voter
{
    public const UPDATE = 'UPDATE_INVOICE';
    public const DELETE = 'DELETE_INVOICE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return ($attribute === self::UPDATE && $subject instanceof Invoice) ||
            ($attribute === self::DELETE && $subject instanceof Invoice);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var Invoice $invoice */
        $invoice = $subject;

        return match ($attribute) {
            self::UPDATE, self::DELETE => $this->canUpdateOrDelete($invoice, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canUpdateOrDelete(Invoice $invoice, User $user): bool
    {
        return $invoice->getDepositAccount()->getUsers()->contains($user);
    }
}
