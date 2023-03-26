<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\AlertEnum;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @method User getUser()
 */
abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function getUserOrThrow(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    protected function addAlert(AlertEnum $type, string $message): void
    {
        $this->addFlash($type->value, $message);
    }
}
