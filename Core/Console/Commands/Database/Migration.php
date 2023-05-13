<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Core\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migration extends Command
{
    use Env;
    protected function configure(): void
    {
        $this->setName('migrate')
            ->setDescription('Run migration')
            ->addOption('fresh', description: 'Fresh all migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $migrations_folder = $this->app_path() . "/database/migrations/";
        $folders = scandir($migrations_folder);
        $migrations = array_filter($folders, fn($files) => preg_match("#^\d#", $files));

        foreach ($migrations as $migration)
        {
            $migration = require $migrations_folder . $migration;
            ($input->getOption('fresh')) ? $migration->down() : $migration->up();
        }

        if ($input->getOption('fresh')) {
            $output->getFormatter()->setStyle("cyan-bg", new OutputFormatterStyle('white', "cyan"));
            $output->writeln("<cyan-bg>DONE</cyan-bg>");
        }
        else {
            $output->getFormatter()->setStyle("bright-magenta-bg", new OutputFormatterStyle('white', "bright-magenta"));
            $output->writeln("<bright-magenta-bg>MIGRATED SUCCESSFULLY</bright-magenta-bg>");
        }

        return Command::SUCCESS;
    }
}