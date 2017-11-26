<?php

namespace JZ\SymfonyPackagesChecker\Analyzer;

interface DependenciesAnalyzer
{
    public function getDependenciesFromPHPFile(string $file);
}