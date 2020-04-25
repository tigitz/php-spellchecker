#!/usr/bin/env php
<?php

declare(strict_types=1);

// hunspell binary stub

$options = getopt('aDd::i:');

if (\array_key_exists('D', $options)) {
    if ($file = file_get_contents(__DIR__ . '/../dicts.txt')) {
        fprintf(STDERR, $file);
        exit(0);
    }

    throw new \Exception('Cannot find dictionary fixtures file');
}

if (\array_key_exists('a', $options)) {
    $language = \array_key_exists('d', $options) ? $options['d'] : false;
    if (!$language && !getenv('LANG')) {
        echo 'Can\'t open affix or dictionary files for dictionary named "default".' . PHP_EOL;
        exit(1);
    }
    echo file_get_contents(__DIR__ . '/../check.txt');
    exit(0);
}

echo 'Invalid call' . PHP_EOL;
exit(1);
