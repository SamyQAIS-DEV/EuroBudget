<?php

namespace App\Command;

use App\Service\InactivityReminderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:inactivity-reminder',
    description: 'Remind users who had no activity since parameter N days',
)]
class InactivityReminderCommand extends Command
{
    public function __construct(private readonly InactivityReminderService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = $this->service->remind();

        $message = ngettext('%d utilisateur a bien été notifié', '%d utilisateurs ont bien été notifiés', $count);
        $message = sprintf($message, $count);
        if ($count === 0) {
            $message = 'Aucun utilisateur n\'a été notifié';
        }

        $io->success($message);

        return Command::SUCCESS;
    }
}
