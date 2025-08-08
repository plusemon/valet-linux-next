<?php

namespace ValetLinuxNext\Commands;

use ValetLinuxNext\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnlinkCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('unlink')
            ->setDescription('Unlink a site')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the linked site');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = new Filesystem();
        $currentDirectory = getcwd();
        $valetConfigDir = getenv('HOME') . '/.config/valet';

        $name = $input->getArgument('name') ?: basename($currentDirectory);

        $linkPath = $valetConfigDir . '/Sites/' . $name;

        if (!$files->exists($linkPath)) {
            $output->writeln('<error>Site [' . $name . '] is not linked.</error>');
            return Command::FAILURE;
        }

        $files->remove($linkPath);

        $output->writeln('<info>Site [' . $name . '] unlinked successfully!</info>');

        return Command::SUCCESS;
    }
}
