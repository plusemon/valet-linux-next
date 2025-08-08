<?php

namespace Gemini\ValetLinuxPlusPlus\Commands;

use Gemini\ValetLinuxPlusPlus\CommandLine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('status')
             ->setDescription('Display the status of Valet services');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cli = new CommandLine();

        $output->writeln('<info>Valet Services Status:</info>');
        $output->writeln('------------------------');

        $this->checkServiceStatus($cli, $output, 'nginx');
        $this->checkServiceStatus($cli, $output, 'dnsmasq');
        $this->checkServiceStatus($cli, $output, 'php8.4-fpm');

        return Command::SUCCESS;
    }

    private function checkServiceStatus(CommandLine $cli, OutputInterface $output, string $serviceName): void
    {
        $output->write(sprintf('%-15s', ucfirst($serviceName) . ': '));
        $status = trim($cli->run('systemctl is-active ' . $serviceName));

        if ($status === 'active') {
            $output->writeln('<info>Running</info>');
        } else {
            $output->writeln('<error>Stopped</error>');
        }
    }
}
