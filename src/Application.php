<?php

namespace Gemini\ValetLinuxPlusPlus;

use Gemini\ValetLinuxPlusPlus\Commands\InstallCommand;
use Gemini\ValetLinuxPlusPlus\Commands\ParkCommand;
use Gemini\ValetLinuxPlusPlus\Commands\LinkCommand;
use Gemini\ValetLinuxPlusPlus\Commands\UnlinkCommand;
use Gemini\ValetLinuxPlusPlus\Commands\UninstallCommand;
use Gemini\ValetLinuxPlusPlus\Commands\StatusCommand;
use Gemini\ValetLinuxPlusPlus\Commands\LinksCommand;
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