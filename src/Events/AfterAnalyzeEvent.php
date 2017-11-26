<?php

namespace JZ\SymfonyPackagesChecker\Events;

use Symfony\Component\EventDispatcher\Event;

class AfterAnalyzeEvent extends Event
{

    const NAME='symfony.analyzer.after';

}