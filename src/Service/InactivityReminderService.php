<?php

namespace App\Service;

use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;

class InactivityReminderService
{
    public const ACTIVITY_REMINDER_NB_DAYS = 7;
    private const MAX_BULK_NB_ITEMS = 5;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailerService $mailer,
    ) {
    }

    public function remind(): int
    {
        $lastActivityDate = new DateTime(sprintf('-%d days', self::ACTIVITY_REMINDER_NB_DAYS));
        $users = $this->userRepository->findInactiveUsers($lastActivityDate, self::MAX_BULK_NB_ITEMS);

        $now = new DateTimeImmutable();
        foreach ($users as $user) {
            $email = $this->mailer->createEmail('mails/activity/inactivity_reminder.twig', 'N\'oubliez pas de tenir vos comptes à jour', [
                'username' => $user->getFullName(),
                'nbDays' => self::ACTIVITY_REMINDER_NB_DAYS,
            ])
                ->to($user->getEmail());
            $this->mailer->send($email);

            $user->setActivityReminderSentAt($now);
            $this->userRepository->save($user);
        }
        $this->userRepository->flush();

        return count($users);
    }
}