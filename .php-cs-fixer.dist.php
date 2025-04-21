<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfig;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'var']);

return (new Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'length'],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
    ->setParallelConfig(new ParallelConfig(4, 20));
