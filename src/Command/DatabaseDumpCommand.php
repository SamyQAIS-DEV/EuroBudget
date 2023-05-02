<?php

namespace App\Command;

use App\Repository\OperationRepository;
use App\Service\Encryptors\EncryptorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:database-dump',
    description: 'Dump the database for backups',
)]
class DatabaseDumpCommand extends Command
{
    private OutputInterface $output;

    private InputInterface $input;

    private string $database;
    private string $username;
    private string $password;
    private string $path;

    private Filesystem $fs;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->entityManager->getConnection();
        dd($conn);

        $path = $input->getArgument('filepath');
        if (! is_dir(dirname($path))) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($path));
        }

        $cmd = sprintf('mysqldump -u %s --password=%s %s %s > %s',
            $conn->getUsername(),
            $conn->getPassword(),
            $conn->getDatabase(),
            implode(' ', ['variables', 'config']),
            $path
        );

        exec($cmd, $output, $exit_status);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute2(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->database = $this->getContainer()->getParameter('database_name') ;
        $this->username = $this->getContainer()->getParameter('database_user') ;
        $this->password = $this->getContainer()->getParameter('database_password') ;
        $this->path = $input->getArgument('file') ;
        $this->fs = new Filesystem() ;
        $this->output->writeln(sprintf('<comment>Dumping <fg=green>%s</fg=green> to <fg=green>%s</fg=green> </comment>', $this->database, $this->path ));
        $this->createDirectoryIfRequired();
        $this->dumpDatabase();
        $output->writeln('<comment>All done.</comment>');
    }

    private function createDirectoryIfRequired() {
        if (! $this->fs->exists($this->path)){
            $this->fs->mkdir(dirname($this->path));
        }
    }

    private function dumpDatabase()
    {
        $cmd = sprintf('mysqldump -B %s -u %s --password=%s' // > %s'
            , $this->database
            , $this->username
            , $this->password
        );

        $result = $this->runCommand($cmd);

        if($result['exit_status'] > 0) {
            throw new \Exception('Could not dump database: ' . var_export($result['output'], true));
        }

        $this->fs->dumpFile($this->path, $result);
    }

    /**
     * Runs a system command, returns the output, what more do you NEED?
     *
     * @param $command
     * @param $streamOutput
     * @param $outputInterface mixed
     * @return array
     */
    protected function runCommand($command)
    {
        $command .=" >&1";
        exec($command, $output, $exit_status);
        return array(
            "output"      => $output
        , "exit_status" => $exit_status
        );
    }
}
