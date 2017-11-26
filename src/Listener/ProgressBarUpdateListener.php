<?php

namespace JZ\SymfonyPackagesChecker\Listener;


use JZ\SymfonyPackagesChecker\Events\AfterAnalyzeEvent;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use JZ\SymfonyPackagesChecker\Events\AfterFileAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeFileAnalyzeEvent;

class ProgressBarUpdateListener
{

    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function createProgressBar(BeforeAnalyzeEvent $event)
    {

        $this->progressBar = new ProgressBar($this->output, $event->getFilesCount());
        $this->progressBar->setFormatDefinition('custom',
            ' %current%/%max% [%bar%] -- %message% (<info>%filename%</info>)');
        $this->progressBar->setFormat('custom');
        $this->progressBar->setMessage('Reading files...');

    }

    public function setFileNameToProgressBar(BeforeFileAnalyzeEvent $event)
    {
        if ($this->progressBar) {
            $this->progressBar->setMessage($event->getFileName(), 'filename');
        }
    }

    public function advanceProgressBar(AfterFileAnalyzeEvent $event)
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }

    public function clearProgressBar(AfterAnalyzeEvent $event)
    {
        if ($this->progressBar) {
            $this->progressBar->clear();
        }

    }
}