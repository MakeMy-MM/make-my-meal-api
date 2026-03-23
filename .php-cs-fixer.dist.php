<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules([
        '@PER-CS' => true,
        'braces_position' => [
            'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'anonymous_functions_opening_brace' => 'same_line',
            'control_structures_opening_brace' => 'same_line',
        ],
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_statement' => ['statements' => ['return']],
        'cast_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'fully_qualified_strict_types' => true,
        'function_typehint_space' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'single_trait_insert_per_statement' => false,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
    ])
    ->setRiskyAllowed(true);
