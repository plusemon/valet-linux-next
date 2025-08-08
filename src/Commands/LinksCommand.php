<?php

namespace Gemini\ValetLinuxPlusPlus\Commands;

use Gemini\ValetLinuxPlusPlus\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinksCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('links')
             ->setDescription('Display all linked Valet sites');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = new Filesystem();
        $configPath = getenv('HOME').'/.config/valet/config.json';

        if (! $files->exists($configPath)) {
            $output->writeln('<comment>No Valet links found.</comment>');
            return Command::SUCCESS;
        }

        $config = json_decode($files->get($configPath), true);

        if (empty($config['paths'])) {
            $output->writeln('<comment>No Valet links found.</comment>');
            return Command::SUCCESS;
        }

        $output->writeln('<info>Valet Links:</info>');
        $output->writeln('-------------------');

        foreach ($config['paths'] as $path) {
            $output->writeln('<comment>' . $path . '</comment>');
            if ($files->exists($path) && is_dir($path)) {
                $sites = array_filter(scandir($path), function ($item) use ($path) {
                    return $item !== '.' && $item !== '..' && is_dir($path . '/' . $item);
                });
                if (empty($sites)) {
                    $output->writeln('  (No sites found in this directory)');
                } else {
                    foreach ($sites as $site) {
                        $output->writeln('  - ' . $site);
                    }
                }
            } else {
                $output->writeln('  (Directory not found or not a directory)');
            }
        }

        return Command::SUCCESS;
    }
}
