<?php

namespace JZ\SymfonyPackagesChecker\Analyzer;

use Hal\Application\Config\ConfigException;
use Hal\Component\Ast\NodeTraverser;
use Hal\Metric\Class_\ClassEnumVisitor;
use Hal\Metric\Class_\Coupling\ExternalsVisitor;
use Hal\Metric\Metrics;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

class PHPMetricsDependenciesAnalyzer implements DependenciesAnalyzer
{

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function getDependenciesFromPHPFile(string $file)
    {

        try {
            $metrics = $this->generateMetrics($file);
        } catch (ConfigException $e) {

            return [];//if we can't generate dependencies - skip
        }

        return $this->getClassesUsedFromMetrics($metrics);

    }

    /**
     * @param $file
     * @return Metrics
     */
    private function generateMetrics($file)
    {

        $metrics = new Metrics();
        $traverser = new NodeTraverser(false, function () {
            return true;
        });
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ClassEnumVisitor($metrics));
        $traverser->addVisitor(new ExternalsVisitor($metrics));

        $code = file_get_contents($file);

        try {
            $stmts = $this->parser->parse($code);
            $traverser->traverse($stmts);

        } catch (\Error $e) {
            // we don't get info from files we can't parse
        }

        return $metrics;
    }

    private function getClassesUsedFromMetrics(Metrics $metrics): array
    {
        $classesUsed = [];
        $types = ['externals', 'parents'];
        foreach ($metrics->all() as $m) {
            foreach ($types as $type) {
                if ($m->get($type)) {
                    foreach ($m->get($type) as $className) {
                        $classesUsed[$className] = 1;
                    }
                }
            }

        }

        return array_keys($classesUsed);
    }
}