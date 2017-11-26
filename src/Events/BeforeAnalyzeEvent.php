<?php

namespace JZ\SymfonyPackagesChecker\Events;

use Symfony\Component\EventDispatcher\Event;

class BeforeAnalyzeEvent extends Event
{
    const NAME='symfony.analyzer.before';
    /**
     * @var int
     */
    private $filesCount;

    public function __construct(int $filesCount)
    {
        $this->filesCount = $filesCount;
    }

    public function getFilesCount(): int
    {
        return $this->filesCount;
    }


}