<?php

namespace JZ\SymfonyPackagesChecker\Analyzer;

use JZ\SymfonyPackagesChecker\Events\AfterAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\AfterFileAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeAnalyzeEvent;
use JZ\SymfonyPackagesChecker\Events\BeforeFileAnalyzeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SymfonyComponentsAnalyzer
{

    /**
     * @var PHPMetricsDependenciesAnalyzer
     */
    private $dependenciesAnalyzer;
    /**
     * @var PHPMetricsPHPFilesFinder
     */
    private $finder;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        DependenciesAnalyzer $usedClassesAnalyzer,
        PHPFilesFinder $finder,
        EventDispatcherInterface $dispatcher
    ) {
        $this->dependenciesAnalyzer = $usedClassesAnalyzer;
        $this->finder = $finder;
        $this->dispatcher = $dispatcher;
    }

    public function getComponentsUsedInDirectory($src)
    {

        $files = $this->finder->findAllPHPFiles($src);
        $components = [];

        $this->dispatcher->dispatch(BeforeAnalyzeEvent::NAME, new BeforeAnalyzeEvent(count($files)));

        // for every PHP file
        foreach ($files as $file) {
            $this->dispatcher->dispatch(BeforeFileAnalyzeEvent::NAME, new BeforeFileAnalyzeEvent($file));

            // find classes used by that file
            $classesUsedInFile = $this->dependenciesAnalyzer->getDependenciesFromPHPFile($file);

            // for every that class
            foreach ($classesUsedInFile as $className) {
                if ($this->isSymfonyClass($className)) {
                    // get symfony component from its name
                    $component = $this->getComponentNameFromClass($className);
                    // add as key to remove duplicates automatically
                    $components[strtolower($component)] = 1;
                } elseif($this->isDoctrineORMClass($className)) {

                    $components['orm-pack']=1;

                } elseif($this->isTwigFile($className)) {
                    $components['templates']=1;
                }
            }

            $this->dispatcher->dispatch(AfterFileAnalyzeEvent::NAME, new AfterFileAnalyzeEvent($file));
        }

        //get keys as we added list as hashmap
        $components = array_keys($components);

        $components = $this->removeDefaultComponents($components);

        $this->dispatcher->dispatch(AfterAnalyzeEvent::NAME, new AfterAnalyzeEvent());
        return $components;

    }

    private function isSymfonyClass($className)
    {
        return substr($className, 0, 8) == 'Symfony\\';
    }

    private function isDoctrineORMClass($className)
    {
        return substr($className, 0, 13) == 'Doctrine\\ORM\\';
    }

    private function isTwigFile($className)
    {
        return substr($className, 0, 4) == 'Twig';
    }

    private function getComponentNameFromClass($className)
    {
        //component, bundle or bridge name is 3rd part in namespace
        $parts = explode('\\', $className);
        if ($parts[1] == 'Bridge') {
            $name = $parts[2] . 'Bridge';
        } else {
            $name = $parts[2];
        }
        //change Uppercase to dash, e.g. TwigBundle -> twig-bundle
        return strtolower(preg_replace('~(?=[A-Z])(?!\A)~', "-", $name));
    }

    private function removeDefaultComponents(array $components): array
    {
        //if we find ANY symfony components, lets assume framework-bundle is needed
        if (count($components) > 0) {
            if (array_search('framework-bundle', $components) === null) {
                $components[] = 'framework-bundle';
            }
        }

        // those components are included with symfony/framework-bundle
        $defaultComponents = [
            'cache',
            'dependency-injection',
            'config',
            'event-dispatcher',
            'http-foundation',
            'http-kernel',
            'filesystem',
            'finder',
            'routing',
        ];
        $components = array_diff($components, $defaultComponents);

        return $components;
    }
}