<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'function_typehint_space' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_superfluous_phpdoc_tags' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_align' => false,
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_order' => true,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_var_annotation_correct_order' => true,
        'protected_to_private' => false,
        'return_assignment' => true,
        'single_line_throw' => false,
        'yoda_style' => false,

    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
