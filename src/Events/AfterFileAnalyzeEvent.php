<?php

namespace JZ\SymfonyPackagesChecker\Events;

use Symfony\Component\EventDispatcher\Event;

class AfterFileAnalyzeEvent extends Event
{

    const NAME='symfony.analyzer.afterfile';

    /**
     * @var string
     */
    private $filesCount;

    public function __construct(string $filesCount)
    {
        $this->filesCount = $filesCount;
    }

    public function getFileName(): string
    {
        return $this->filesCount;
    }


}