<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'specs', 'assets/dist', 'var'])
    ->notPath('tests/Fixtures/TestApp/config/reference.php')
    ->notName('*.twig')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP83Migration' => true,
        'declare_strict_types' => true,
        'ordered_imports' => true,
        'single_line_throw' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
