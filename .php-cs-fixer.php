<?php

// .php-cs-fixer.php for MyBB

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@Symfony' => true,
    // Alias - https://cs.symfony.com/doc/rules/index.html#alias
    'array_push' => true,
    'ereg_to_preg' => true,
    'mb_str_functions' => true,
    'no_alias_functions' => ['sets' => ['@all']],
    'random_api_migration' => true,
    'set_type_to_cast' => true,
    // Cast Notation - https://cs.symfony.com/doc/rules/index.html#cast-notation
    'modernize_types_casting' => true,
    // Class Notation - https://cs.symfony.com/doc/rules/index.html#class-notation
    'class_attributes_separation' => true,
    'final_class' => true,
    'final_internal_class' => true,
    'final_public_method_for_abstract_class' => true,
    'modifier_keywords' => true,
    'no_unneeded_final_method' => true,
    'ordered_interfaces' => true,
    'ordered_traits' => true,
    'ordered_types' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha'
    ],
    'protected_to_private' => true,
    'self_accessor' => true,
    'self_static_accessor' => true,
    // Class Usage - https://cs.symfony.com/doc/rules/index.html#class-usage
    'date_time_immutable' => true,
    // Control Structure - https://cs.symfony.com/doc/rules/index.html#control-structure
    'no_superfluous_elseif' => true,
    'no_useless_else' => true,
    'simplified_if_return' => true,
    'yoda_style' => [
        'equal' => false,
        'identical' => false,
        'less_and_greater' => false,
    ],
    // Function Notation - https://cs.symfony.com/doc/rules/index.html#function-notation
    'combine_nested_dirname' => true,
    'method_argument_space' => [
        'after_heredoc' => false,
        'attribute_placement' => 'ignore',
        'on_multiline' => 'ensure_fully_multiline'
    ],
    'native_function_invocation' => true,
    'no_unreachable_default_argument_value' => true,
    'regular_callable_call' => true,
    // Import - https://cs.symfony.com/doc/rules/index.html#import
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    // Language Construct - https://cs.symfony.com/doc/rules/index.html#language-construct
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'dir_constant' => true,
    // List Notation - https://cs.symfony.com/doc/rules/index.html#list-notation
    'list_syntax' => true,
    // Operator - https://cs.symfony.com/doc/rules/index.html#operator
    'assign_null_coalescing_to_coalesce_equal' => true,
    'binary_operator_spaces' => [
        'operators' => [
            '=>' => 'align',
        ],
    ],
    'concat_space' => ['spacing' => 'one'],
    'increment_style' => ['style' => 'post'],
    'logical_operators' => true,
    'long_to_shorthand_operator' => true,
    'not_operator_with_successor_space' => true,
    'operator_linebreak' => ['only_booleans' => false],
    'ternary_to_null_coalescing' => true,
    // PHPUnit - https://cs.symfony.com/doc/rules/index.html#phpunit
    'php_unit_method_casing' => ['case' => 'snake_case'],
    // PHPDoc - https://cs.symfony.com/doc/rules/index.html#phpdoc
    'no_blank_lines_after_phpdoc' => false,
    'no_superfluous_phpdoc_tags' => [
        'allow_hidden_params' => true,
        'allow_mixed' => true,
        'allow_unused_params' => true,
        'remove_inheritdoc' => true
    ],
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_annotation_without_dot' => false,
    'phpdoc_return_self_reference' => true,
    'phpdoc_summary' => false,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha'
    ],
    // Return Notation - https://cs.symfony.com/doc/rules/index.html#return-notation
    'no_useless_return' => true,
    // Semicolon - https://cs.symfony.com/doc/rules/index.html#semicolon
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    // Strict - https://cs.symfony.com/doc/rules/index.html#strict
    'declare_strict_types' => true,
    'strict_comparison' => true,
    // Whitespace - https://cs.symfony.com/doc/rules/index.html#whitespace
    'blank_line_before_statement' => true,
    'method_chaining_indentation' => true,
    'no_extra_blank_lines' => ['tokens' => ['use']],
];

$finder = Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true);

return (new Config)
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
