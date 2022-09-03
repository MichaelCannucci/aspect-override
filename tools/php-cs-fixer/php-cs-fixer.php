<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/../../src');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'curly_braces_position' => [
        'functions_opening_brace' => 'same_line',
        'classes_opening_brace' => 'same_line'
    ],
])
    ->setFinder($finder);
