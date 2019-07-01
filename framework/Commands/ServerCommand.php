<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;

class ServerCommand extends Command
{
    protected static $defaultName = 'run';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->addOption('explorer', 'e', InputArgument::OPTIONAL, 'The explorer to open.');
        $this->addOption('port', 'p', InputArgument::OPTIONAL, 'The server application port.');
        $this->addOption('scheme', 's', InputArgument::OPTIONAL, 'The server application scheme.');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpVersion = phpversion();

        $scheme = $input->getOption('scheme') ?? 'http';
        $host = 'localhost';
        $port = $input->getOption('port') ?? '3000';
        $url = "{$scheme}://{$host}:{$port}";

        $now = Carbon::now();

        $publicFolder = 'public';

        Open::open($url, $input->getOption('explorer'));

        $output->writeln("PHP {$phpVersion} Development Server started at {$now}");
        $output->writeln("Listening on {$url}:{$port}");
        $output->writeln("Running Amber Framework v-beta.");
        $output->writeln("Never use this server on production.");
        $output->writeln("Press Ctrl-C to quit.");
        
        exec("php -S {$host}:{$port} -t {$publicFolder}");
    }
}
