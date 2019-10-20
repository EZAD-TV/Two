<?php

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Two\Container\ServiceSetCollector;

require_once __DIR__ . '/../vendor/autoload.php';

$serviceCollector = new ServiceSetCollector('Service');
$factoryCollector = new ServiceSetCollector('Factory');

$nameResolver = new NameResolver();
$traverser = new NodeTraverser();
$traverser->addVisitor($nameResolver);
$traverser->addVisitor($serviceCollector);
$traverser->addVisitor($factoryCollector);

$parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

// loop through all files that can contain service definitions...
$stmts = $parser->parse(file_get_contents($_SERVER['argv'][1]));
$traverser->traverse($stmts);

// dump out the phpstorm metadata into a file in the application's .phpstorm.meta.php/ folder
writeMetaRules($serviceCollector, 'Service', 'two_services');
writeMetaRules($factoryCollector, 'Factory', 'two_factories');

function writeMetaRules(ServiceSetCollector $collector, $svcClass, $setName)
{
    if ($collector->argumentSet) {
        echo "registerArgumentsSet('$setName'";
        foreach ($collector->argumentSet as $className => $x) {
            echo ",\n    $className";
        }
        echo ");\n\n";
        echo "expectedArguments(\\Two\\$svcClass::get(), 0, argumentsSet('$setName'));\n\n";
    }

    if ($collector->overrides) {
        echo "override(\\Two\\$svcClass::get(0), map([\n";
        foreach ($collector->overrides as $alias => $returnValue) {
            echo "    '$alias' => $returnValue,\n";
        }
        echo "]));\n\n";
    }
}