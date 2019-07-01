<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Schema\Builder as Schema;
use Amber\Container\Application;

class MigrateSeedsCommand extends Command
{
    protected static $defaultName = 'migrate:seeds';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running seeds.</comment>');

        $seeds = Application::make(\DatabaseSeeder::class)->seeds();

        foreach ($seeds as $seed) {
            $output->writeln("Seeding {$seed} in progress.");
            Application::make($seed)->run();
            $output->writeln("<info>Seeding {$seed} done.</info>");
        }
    }
}
