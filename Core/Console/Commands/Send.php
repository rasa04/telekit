<?php

namespace Core\Console\Commands;

use Core\Database\Database;
use Core\Methods\SendMessage;
use Database\models\Chat;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Send extends Command
{
    protected function configure(): void
    {
        $this->setName('send')
            ->setDescription('Send message')
            ->addArgument('all', InputArgument::OPTIONAL)
            ->addOption('to', 't', InputOption::VALUE_OPTIONAL)
            ->addOption('message', 'm', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $input->getOptions();

        if (isset($options['to'])) {
            if (!isset($options['message'])) {
                $output->getFormatter()->setStyle("yellow-bg", new OutputFormatterStyle('black', "yellow"));
                $output->writeln("<yellow-bg> WARING: You didn't specify the message </yellow-bg>");
            }
            $chat_id = $options["to"];
            $message = $options["message"] ?? "Hi! It's test message from " . getenv("APP_NAME");
            (new SendMessage)
                ->chat_id($chat_id)
                ->text($message)
                ->send();
        }
        elseif ($input->getArgument('all')) {
            new Database();
            $chats = Chat::all()->pluck('chat_id')->toArray();
            $message = $options["message"] ?? "Hi! It's test message from " . getenv("APP_NAME");

            foreach ($chats as $chat) {
                try {
                    (new SendMessage)
                        ->chat_id($chat)
                        ->text($message)
                        ->send();
                    $output->getFormatter()->setStyle("green-bg", new OutputFormatterStyle('black', "green"));
                    $output->writeln("<green-bg> Shipped: $chat </green-bg>");
                }
                catch (Exception $e) {
                    if (strpos($e->getMessage(), "chat not found")) {
                        $output->getFormatter()->setStyle("yellow-bg", new OutputFormatterStyle('black', "yellow"));
                        $output->writeln("<yellow-bg> No chat: $chat </yellow-bg>");
                    }
                }
            }

        }
        else {
            $output->getFormatter()->setStyle("yellow-bg", new OutputFormatterStyle('black', "yellow"));
            $output->writeln("<yellow-bg> Missed parameter to </yellow-bg>");
        }
        return Command::SUCCESS;
    }
}