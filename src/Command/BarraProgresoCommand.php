<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BarraProgresoCommand extends Command
{
    protected static $defaultName = 'BarraProgresoCommand';

    protected function configure()
    {
        $this
            ->setName('app:progress_bar_command')
            //->setDescription('Add a short description for your command')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');*/
        $progressBar = new ProgressBar($output, 1000);
        $progressBar->start();

        for ($i = 0; $i < 1000; $i++) {
            $progressBar->advance();
            usleep(1000); // sleep a little bit
        }

        $progressBar->finish();
    }
}
