<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Params extends Command
{
    protected function configure(): void
    {
        $this->setName('database:params')->setDescription('Database parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        var_dump(Capsule::connection()->getConfig());
        return Command::SUCCESS;
    }
}