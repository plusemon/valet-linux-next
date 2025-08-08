<?php

namespace ValetLinuxNext\Commands;

use ValetLinuxNext\CommandLine;
use ValetLinuxNext\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('install')
            ->setDescription('Install Valet Linux Next');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cli = new CommandLine();
        $files = new Filesystem();

        $output->writeln('<info>Installing Valet Linux Next...</info>');

        $this->installNginx($cli, $files, $output);
        $this->installDnsmasq($cli, $files, $output);
        $this->disableSystemdResolved($cli, $output);
        $this->installPhpFpm($cli, $output);
        $this->createValetDirectories($files, $output);
        $this->symlinkValetExecutable($cli, $output);

        $output->writeln('<info>Valet Linux Next installed successfully!</info>');

        return Command::SUCCESS;
    }

    private function isPackageInstalled(CommandLine $cli, string $packageName): bool
    {
        $process = $cli->run('dpkg -s ' . $packageName);
        return str_contains($process, 'Status: install ok installed');
    }

    private function installNginx(CommandLine $cli, Filesystem $files, OutputInterface $output)
    {
        if ($this->isPackageInstalled($cli, 'nginx')) {
            $output->writeln('<comment>Nginx is already installed. Skipping installation.</comment>');
        } else {
            $output->writeln('<info>Installing Nginx...</info>');
            $cli->run('sudo apt-get update', function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
            $cli->run('sudo apt-get install -y nginx', function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        }

        $files->put('/etc/nginx/sites-available/valet.conf', $this->getNginxConfig());
        $cli->run('sudo ln -snf /etc/nginx/sites-available/valet.conf /etc/nginx/sites-enabled/valet.conf');
        $cli->run('sudo service nginx restart');

        $output->writeln('<info>Nginx configured successfully!</info>');
    }

    private function installDnsmasq(CommandLine $cli, Filesystem $files, OutputInterface $output)
    {
        if ($this->isPackageInstalled($cli, 'dnsmasq')) {
            $output->writeln('<comment>Dnsmasq is already installed. Skipping installation.</comment>');
        } else {
            $output->writeln('<info>Installing Dnsmasq...</info>');
            $cli->run('sudo apt-get install -y dnsmasq', function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        }

        $files->put('/etc/dnsmasq.d/valet', 'address=/.test/127.0.0.1');
        $this->configureResolvConf($cli, $files, $output);
        $cli->run('sudo systemctl restart dnsmasq');

        $output->writeln('<info>Dnsmasq configured successfully!</info>');
    }

    private function disableSystemdResolved(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('<info>Disabling and stopping systemd-resolved...</info>');
        $cli->run('sudo systemctl disable systemd-resolved');
        $cli->run('sudo systemctl stop systemd-resolved');
        $output->writeln('<info>systemd-resolved disabled and stopped.</info>');
    }

    private function configureResolvConf(CommandLine $cli, Filesystem $files, OutputInterface $output)
    {
        $output->writeln('<info>Configuring /etc/resolv.conf...</info>');
        $resolvConfPath = '/etc/resolv.conf';

        // Ensure /etc/resolv.conf is a regular file, not a symlink
        if ($files->isLink($resolvConfPath)) {
            $cli->run('sudo unlink ' . $resolvConfPath);
            $cli->run('sudo touch ' . $resolvConfPath);
        }

        $newContent = "nameserver 127.0.0.1\nnameserver 8.8.8.8\n"; // Valet DNS + Google DNS
        $files->put($resolvConfPath, $newContent);
        $output->writeln('<info>/etc/resolv.conf configured.</info>');
    }

    private function installPhpFpm(CommandLine $cli, OutputInterface $output)
    {
        if ($this->isPackageInstalled($cli, 'php-fpm')) {
            $output->writeln('<comment>PHP-FPM is already installed. Skipping installation.</comment>');
        } else {
            $output->writeln('<info>Installing PHP-FPM...</info>');
            $cli->run('sudo apt-get install -y php-fpm', function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        }
        $output->writeln('<info>PHP-FPM installed successfully!</info>');
    }

    private function createValetDirectories(Filesystem $files, OutputInterface $output)
    {
        $output->writeln('<info>Creating Valet directories...</info>');
        $files->ensureDirExists(getenv('HOME') . '/.config/valet');
        $files->ensureDirExists(getenv('HOME') . '/valet');
        $output->writeln('<info>Valet directories created successfully!</info>');
    }

    private function symlinkValetExecutable(CommandLine $cli, OutputInterface $output)
    {
        $output->writeln('<info>Symlinking Valet executable...</info>');
        $cli->run('sudo ln -snf ' . getcwd() . '/valet /usr/local/bin/valet');
        $output->writeln('<info>Valet executable symlinked successfully!</info>');
    }

    private function getNginxConfig(): string
    {
        return <<<'EOT'
server {
    listen 80;
    server_name ~\.test$;

    root /home/emon/Desktop/valet-linux-plus-plus/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
    }
}
EOT;
    }
}