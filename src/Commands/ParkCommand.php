<?php

namespace Gemini\ValetLinuxPlusPlus\Commands;

use Gemini\ValetLinuxPlusPlus\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParkCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('park')
             ->setDescription('Park the current working directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = new Filesystem();
        $currentDirectory = getcwd();
        $valetConfigDir = getenv('HOME').'/.config/valet';
        $configPath = $valetConfigDir.'/config.json';

        $files->ensureDirExists($valetConfigDir);

        $config = [];
        if ($files->exists($configPath)) {
            $config = json_decode($files->get($configPath), true);
        }

        if (!isset($config['paths'])) {
            $config['paths'] = [];
        }

        if (in_array($currentDirectory, $config['paths'])) {
            $output->writeln('<info>This directory is already parked.</info>');
            return Command::SUCCESS;
        }

        $config['paths'][] = $currentDirectory;
        $files->put($configPath, json_encode($config, JSON_PRETTY_PRINT));

        $output->writeln('<info>['.$currentDirectory.'] parked successfully!</info>');

        return Command::SUCCESS;
    }
}
