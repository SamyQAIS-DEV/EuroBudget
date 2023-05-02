<?php

namespace App\Command;

use App\Repository\OperationRepository;
use App\Service\Encryptors\EncryptorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:encrypt',
    description: 'Encrypt given data that can be copied and paste somewhere mannually',
)]
class EncryptCommand extends Command
{
    public function __construct(private readonly EncryptorInterface $encryptor)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('data', InputArgument::REQUIRED, 'Data to encrypt is required (must be a string)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $input->getArgument('data');

        if (!$data) {
            $io->error('You need to pass data (string) to encrypt');
            return Command::FAILURE;
        }

        $io->success(sprintf('Your encrypted data for "%s" is: %s', $data, $this->encryptor->encrypt($data)));

        return Command::SUCCESS;
    }
}
