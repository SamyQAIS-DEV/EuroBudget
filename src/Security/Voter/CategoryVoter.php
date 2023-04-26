<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
    public const ACCESS = 'ACCESS_CATEGORIES';
    public const UPDATE = 'UPDATE_CATEGORY';
    public const DELETE = 'DELETE_CATEGORY';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ACCESS ||
            ($attribute === self::UPDATE && $subject instanceof Category) ||
            ($attribute === self::DELETE && $subject instanceof Category);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var Category $category */
        $category = $subject;

        return match ($attribute) {
            self::ACCESS => $user instanceof User && $user->isPremium(),
            self::UPDATE, self::DELETE => $this->canUpdateOrDelete($category, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canUpdateOrDelete(Category $category, User $user): bool
    {
        return $category->getDepositAccount()->getUsers()->contains($user);
    }
}
