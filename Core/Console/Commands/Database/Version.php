<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Version extends Command
{
    protected function configure(): void
    {
        $this->setName('database:version')->setDescription('Version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        var_dump(Capsule::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION));
        return Command::SUCCESS;
    }
}