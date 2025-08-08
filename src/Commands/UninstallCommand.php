<?php

namespace Gemini\ValetLinuxPlusPlus\Commands;

use Gemini\ValetLinuxPlusPlus\CommandLine;
use Gemini\ValetLinuxPlusPlus\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

class UninstallCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('uninstall')
             ->setDescription('Uninstall Valet Linux Next');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cli = new CommandLine();
        $files = new Filesystem();
        $helper = new QuestionHelper();

        $output->writeln('Uninstalling Valet Linux Next...');

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to revert Dnsmasq configuration? (y/N)</question> ', false))) {
            $this->uninstallDnsmasq($cli, $output);
        }

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to revert Nginx configuration (remove valet.conf and restart Nginx)? (y/N)</question> ', false))) {
            $this->revertNginxConfig($cli, $output);
        }

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to revert /etc/resolv.conf changes and re-enable systemd-resolved? (y/N)</question> ', false))) {
            $this->revertResolvConf($cli, $files, $output);
        }

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to uninstall PHP-FPM? (y/N)</question> ', false))) {
            $this->uninstallPhpFpm($cli, $output);
        }

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to remove Valet directories (~/.config/valet and ~/valet)? (y/N)</question> ', false))) {
            $this->removeValetDirectories($files, $output);
        }

        if ($helper->ask($input, $output, new ConfirmationQuestion('<question>Do you want to remove the Valet executable from /usr/local/bin? (y/N)</question> ', false))) {
            $this->removeValetExecutable($cli, $output);
        }

        $output->writeln('Valet Linux Next uninstalled successfully!');

        return Command::SUCCESS;
    }

    private function uninstallDnsmasq(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('Reverting Dnsmasq configuration...');
        $cli->run('sudo rm -f /etc/dnsmasq.d/valet');
        $cli->run('sudo rm -f /etc/dnsmasq.d/valet-upstream.conf');
        $cli->run('sudo service dnsmasq stop');
        $output->writeln('Dnsmasq configuration reverted.');
    }

    private function revertResolvConf(CommandLine $cli, Filesystem $files, OutputInterface $output)
    {
        $output->writeln('Reverting /etc/resolv.conf...');
        $resolvConfPath = '/etc/resolv.conf';

        // Re-enable and start systemd-resolved first
        $output->writeln('Re-enabling systemd-resolved...');
        $cli->run('sudo systemctl enable systemd-resolved');
        $cli->run('sudo systemctl start systemd-resolved');

        // Remove the resolv.conf file. systemd-resolved will recreate it.
        if ($files->exists($resolvConfPath)) {
            $cli->run('sudo rm -f ' . $resolvConfPath);
        }
        $output->writeln('/etc/resolv.conf reverted and systemd-resolved re-enabled.');
    }

    private function revertNginxConfig(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('Reverting Nginx configuration...');
        $cli->run('sudo rm -f /etc/nginx/sites-enabled/valet.conf');
        $cli->run('sudo rm -f /etc/nginx/sites-available/valet.conf');
        $cli->run('sudo service nginx stop');
        $output->writeln('Nginx configuration reverted.');
    }

    private function uninstallPhpFpm(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('Stopping PHP-FPM...');
        $cli->run('sudo systemctl stop php8.4-fpm');
        $output->writeln('PHP-FPM stopped successfully!');
    }

    private function removeValetDirectories(Filesystem $files, OutputInterface $output)
    {
        $output->writeln('Removing Valet directories...');
        $files->remove(getenv('HOME').'/.config/valet');
        $files->remove(getenv('HOME').'/valet');
        $output->writeln('Valet directories removed successfully!');
    }

    private function removeValetExecutable(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('Removing Valet executable from /usr/local/bin...');
        $cli->run('sudo rm -f /usr/local/bin/valet');
        $output->writeln('Valet executable removed successfully!');
    }
}
