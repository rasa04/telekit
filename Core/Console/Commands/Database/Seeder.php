<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Core\Env;
use Database\seeders\DatabaseSeeder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seeder extends Command
{
    use Env;
    protected function configure(): void
    {
        $this->setName('seed')
            ->setDescription('Run database seeder')
            ->addOption('fresh');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $seeder = new DatabaseSeeder();
        $seeder->run();
        $output->getFormatter()->setStyle("green-bg", new OutputFormatterStyle('white', "green"));
        $output->writeln("<green-bg>SEEDED <green-bg>");

        return Command::SUCCESS;
    }
}