<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Amber\Container\Facades\Amber;

class ServerCommand extends Command
{
    protected static $defaultName = 'run';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->addOption('explorer', 'e', InputOption::VALUE_OPTIONAL, 'The explorer to open.', false);
        $this->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'The server application port.', 3000);
        $this->addOption('scheme', 's', InputOption::VALUE_OPTIONAL, 'The server application scheme.', 'http');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpVersion = phpversion();

        $scheme = $input->getOption('scheme');
        $host = 'localhost';
        $port = $input->getOption('port');
        $url = "{$scheme}://{$host}:{$port}";

        $running = Amber::name() . ' ' . Amber::version();

        $now = Carbon::now();

        $publicFolder = 'public';

        $explorer = $input->getOption('explorer');

        if ($explorer !== false) {
            Open::open($url, $input->getOption('explorer'));
        }

        $output->writeln("PHP {$phpVersion} Development Server started at {$now}");
        $output->writeln("Listening on {$url}");
        $output->writeln("Running {$running}.");
        $output->writeln("Never use this server on production.");
        $output->writeln("Press Ctrl-C to quit.");
        
        exec("php -S {$host}:{$port} -t {$publicFolder}");
    }
}
