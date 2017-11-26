<?php

namespace JZ\SymfonyPackagesChecker\Command;

use JZ\SymfonyPackagesChecker\Analyzer\SymfonyComponentsAnalyzer;
use JZ\SymfonyPackagesChecker\Events\AfterAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\AfterFileAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeFileAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Listener\ProgressBarUpdateListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckCommand extends Command
{

    /**
     * @var SymfonyComponentsAnalyzer
     */
    private $analyzer;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(SymfonyComponentsAnalyzer $analyzer, EventDispatcherInterface $eventDispatcher)
    {

        $this->analyzer = $analyzer;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("check")
            ->setDescription("Command to find which symfony components/bundles you are using!")
            ->addArgument('dir', InputArgument::REQUIRED, 'Path to src\ directory to check for symfony components');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $src = $input->getArgument('dir');

        if (!file_exists($src)) {
            throw new InvalidArgumentException(sprintf("Sorry! %s doesn't exists", $src));
        }
        if (!is_dir($src)) {
            throw new InvalidArgumentException(sprintf("Sorry! %s is not a directory", $src));
        }

        $listener = new ProgressBarUpdateListener($output);
        $this->eventDispatcher->addListener(BeforeAnalyzeEvent::NAME, [$listener, 'createProgressBar']);
        $this->eventDispatcher->addListener(BeforeFileAnalyzeEvent::NAME, [$listener, 'setFileNameToProgressBar']);
        $this->eventDispatcher->addListener(AfterFileAnalyzeEvent::NAME, [$listener, 'advanceProgressBar']);
        $this->eventDispatcher->addListener(AfterAnalyzeEvent::NAME, [$listener, 'clearProgressBar']);

        $output->write("Looking for PHP files...");

        $components = $this->analyzer->getComponentsUsedInDirectory($src);

        if (empty($components)) {
            $output->writeln("<info>Looks like you are not using any Symfony components in given directory!</info>");
            return 0;
        }

        $output->writeln("<info>Looks like you are using following Symfony packages:</info>");

        foreach ($components as $component) {
            $output->writeln(sprintf("symfony/%s", $component));
        }

        return 0;
    }


}