<?php

namespace Core\Console\Commands;

use Core\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class Responses extends Command
{
    use Env;
    protected function configure()
    {
        $this->setName('responses')
            ->setDescription('Show all responses')
            ->addArgument('name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $responses = array_map(function ($trigger, $inline, $callback, $invoice) {
                return [
                    str_replace('.php', '', $trigger ?? ''),
                    str_replace('.php', '', $inline ?? ''),
                    str_replace('.php', '', $callback ?? ''),
                    str_replace('.php', '', $invoice ?? '')
                ];
            },
            scandir(self::app_path() . '/responses/Triggers'),
            scandir(self::app_path() . '/responses/Inlines'),
            scandir(self::app_path() . '/responses/Callbacks'),
            scandir(self::app_path() . '/responses/Invoices'),
        );

        $table = new Table($output);
        $table->setHeaders(['TRIGGERS', 'INLINES', 'CALLBACKS', 'INVOICES'])
            ->setRows(array_slice($responses, 2))
            ->render();
        return Command::SUCCESS;
    }
}