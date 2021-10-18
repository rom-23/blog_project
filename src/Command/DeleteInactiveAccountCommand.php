<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class DeleteInactiveAccountCommand extends Command
{
    private EntityManagerInterface $em;
    private SymfonyStyle $io;
    protected static $defaultName = 'app:delete-inactive-accounts';

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Delete inactive accounts in database');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteInactiveAccounts();
        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function deleteInactiveAccounts()
    {
        $this->io->section('Deleting inactive accounts in database');
        $sql            = "DELETE FROM user WHERE account_must_be_verified_before < NOW() AND is_verified = false";
        $db             = $this->em->getConnection();
        $statement      = $db->executeQuery($sql);
        $accountDeleted = $statement->rowCount();
        if ($accountDeleted > 1) {
            $string = "{$accountDeleted} accounts have been removed from database";
        } elseif ($accountDeleted === 1) {
            $string = 'Only 1 inactive account has been removed from database';
        } else {
            $string = 'No inactive accounts detected';
        }
        $this->io->success($string);
    }
}
