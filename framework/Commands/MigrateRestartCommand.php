<?php

namespace Amber\Framework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class MigrateRestartCommand extends Command
{
    protected static $defaultName = 'migrate:restart';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->find('migrate:down')->run(new ArrayInput([]), $output);

        $output->writeln('');

        $this->getApplication()->find('migrate:up')->run(new ArrayInput([]), $output);

        $output->writeln('');

        $this->getApplication()->find('migrate:seed')->run(new ArrayInput([]), $output);
    }
}
