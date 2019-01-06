#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * hunspell binary stub
 */

$options = getopt('aDd::i:');

if (array_key_exists('D', $options)) {
    fprintf(STDERR, file_get_contents(__DIR__ . '/../dicts.txt'));
    exit(0);
}

if (array_key_exists('a', $options)) {
    $language = array_key_exists('d', $options) ? $options['d'] : false;
    if (!$language && !getenv('LANG')) {
        echo 'Can\'t open affix or dictionary files for dictionary named "default".' . PHP_EOL;
        exit(1);
    }
    echo file_get_contents(__DIR__ . '/../check.txt');
    exit(0);
}

echo 'Invalid call' . PHP_EOL;
exit(1);
