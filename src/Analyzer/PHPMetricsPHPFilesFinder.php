<?php

namespace JZ\SymfonyPackagesChecker\Analyzer;

use Hal\Component\File\Finder;

class PHPMetricsPHPFilesFinder implements PHPFilesFinder
{

    public function findAllPHPFiles(string $directory): array
    {
        $finder = new Finder();
        return $finder->fetch([$directory]);
    }

}