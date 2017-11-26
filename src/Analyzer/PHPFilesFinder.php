<?php

namespace JZ\SymfonyPackagesChecker\Analyzer;

interface PHPFilesFinder
{
    public function findAllPHPFiles(string $directory): array;
}