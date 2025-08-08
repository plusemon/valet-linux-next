<?php

namespace ValetLinuxNext;

use ValetLinuxNext\Commands\InstallCommand;
use ValetLinuxNext\Commands\ParkCommand;
use ValetLinuxNext\Commands\LinkCommand;
use ValetLinuxNext\Commands\UnlinkCommand;
use ValetLinuxNext\Commands\UninstallCommand;
use ValetLinuxNext\Commands\StatusCommand;
use ValetLinuxNext\Commands\LinksCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('Valet Linux Next', '1.0.0');
    }

    protected function getDefaultCommands(): array
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new InstallCommand();
        $commands[] = new ParkCommand();
        $commands[] = new LinkCommand();
        $commands[] = new UnlinkCommand();
        $commands[] = new UninstallCommand();
        $commands[] = new StatusCommand();
        $commands[] = new LinksCommand();
        return $commands;
    }
}