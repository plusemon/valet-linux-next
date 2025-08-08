<?php

namespace Gemini\ValetLinuxPlusPlus\Commands;

use Gemini\ValetLinuxPlusPlus\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('link')
             ->setDescription('Link a directory to Valet')
             ->addArgument('name', InputArgument::OPTIONAL, 'The name of the link');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = new Filesystem();
        $currentDirectory = getcwd();
        $valetConfigDir = getenv('HOME').'/.config/valet';

        $name = $input->getArgument('name') ?: basename($currentDirectory);

        $files->ensureDirExists($valetConfigDir.'/Sites');

        $linkPath = $valetConfigDir.'/Sites/'.$name;

        if ($files->exists($linkPath)) {
            $output->writeln('<error>A site with this name already exists.</error>');
            return Command::FAILURE;
        }

        $files->symlink($currentDirectory, $linkPath);

        $output->writeln('<info>['.$currentDirectory.'] linked to ['.$name.'.test] successfully!</info>');

        return Command::SUCCESS;
    }
}
