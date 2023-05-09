<?php

namespace Core\Console\Commands\Make;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigration extends Command
{
    use Paths;
    protected function configure()
    {
        $this->setName('make:migration')
            ->setDescription('To make new model')
            ->addArgument('name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $file = self::samplesPath() . "Migration.php";
        $new_file = self::databasePath() . "migrations\\" . date("Y_m_d") . time() . "_create_${name}_table.php";
        copy($file, $new_file);

        $output->getFormatter()->setStyle("bright-green-bg", new OutputFormatterStyle('black', "bright-green"));
        $output->writeln("<bright-green-bg> NEW MIGRATION CREATED ON: " . realpath($new_file) . " <bright-green-bg>");
        return Command::SUCCESS;
    }
}