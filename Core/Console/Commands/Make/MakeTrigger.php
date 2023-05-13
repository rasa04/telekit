<?php

namespace Core\Console\Commands\Make;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeTrigger extends Command
{
    use Paths;
    protected function configure()
    {
        $this->setName('make:trigger')
            ->setDescription('To make new trigger')
            ->addArgument('name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $file = self::samplesPath() . "Trigger.php";
        $new_file = self::responsesPath() . "Responses/" . $name . ".php";
        copy($file, $new_file);

        $output->getFormatter()->setStyle("bright-green-bg", new OutputFormatterStyle('black', "bright-green"));
        $output->writeln("<bright-green-bg> NEW TRIGGER CREATED ON: " . realpath($new_file) . " <bright-green-bg>");
        return Command::SUCCESS;
    }
}