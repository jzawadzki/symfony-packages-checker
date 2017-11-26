<?php

namespace JZ\SymfonyPackagesChecker;

use JZ\SymfonyPackagesChecker\Command\CheckCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('Symfony 4 upgrade helper', '0.1.0');
        $serviceLocator = new ServiceLocator();
        $this->add(new CheckCommand($serviceLocator->get('analyzer'), $serviceLocator->get('event.dispatcher')));
    }

}