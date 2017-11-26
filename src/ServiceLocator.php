<?php

namespace JZ\SymfonyPackagesChecker;

use JZ\SymfonyPackagesChecker\Analyzer\PHPMetricsDependenciesAnalyzer;
use JZ\SymfonyPackagesChecker\Analyzer\PHPMetricsPHPFilesFinder;
use JZ\SymfonyPackagesChecker\Analyzer\SymfonyComponentsAnalyzer;
use PhpParser\ParserFactory;
use Symfony\Component\DependencyInjection\ServiceLocator as BaseServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;


class ServiceLocator extends BaseServiceLocator
{
    private $services = [];

    public function __construct()
    {
        parent::__construct([
            'event.dispatcher' => function () {
                return $this->services['event.dispatcher'] = $this->services['event.dispatcher'] ?? new EventDispatcher();
            },
            'analyzer' => function () {
                return $this->services['analyzer'] = $this->services['analyzer'] ?? $this->createAnalyzer();
            },
        ]);
    }

    private function createAnalyzer()
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $dependenciesAnalyzer = new PHPMetricsDependenciesAnalyzer($parser);

        $filesFinder = new PHPMetricsPHPFilesFinder();

        return new SymfonyComponentsAnalyzer($dependenciesAnalyzer, $filesFinder, $this->get('event.dispatcher'));
    }

}