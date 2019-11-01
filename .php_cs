<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'no_unused_imports' => true,
        'blank_line_before_statement' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_to_return_type' => true,
        'no_empty_phpdoc' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'phpdoc_var_annotation_correct_order' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'binary_operator_spaces' => true,
        'single_quote' => true,
        'class_attributes_separation' => ['elements' => ['method', 'property']],
        'no_whitespace_in_blank_line' => true,
    ])
    ->setFinder($finder)
    ;
