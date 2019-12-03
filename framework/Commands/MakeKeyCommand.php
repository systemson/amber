<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Amber\Helpers\Crypto\Hash;

class MakeKeyCommand extends Command
{
    protected static $defaultName = 'make:key';

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<comment>Your new key is:</comment>");

        $key = Hash::key();
        $output->writeln("<info>{$key}</info>");

        $output->writeln('Write it down and store it safely.');
    }
}
