<?php

use function Symfony\Component\String\u;

require_once __DIR__ . '/../vendor/autoload.php';

$test = \Symfony\Component\String\b('ça éxagèrre')->bytesAt(0);
$test9 = \Symfony\Component\String\u('नमस्ते')->toByteString('windows-1252');
//$test6 = \Symfony\Component\String\b('ça éxagèrre')->toByteString('windows-1252')->toCodePointString('Windows-1252');
$test7 = \Symfony\Component\String\b('ça éxagèrre')->toCodePointString('utf8');
$test7zs = "\u{0063}";
$test7zsz = ord('c');
$test2 = \Symfony\Component\String\u('नमस्ते')->length();
$test4 = strlen('नमस्ते');
$test5 = mb_strlen('नमस्ते');
$testr = unpack('C*','नमस्ते');
$testraas = unpack('C*',\Symfony\Component\String\b('ça éxaœèrre')->toByteString('ASCII'));
$testraa = unpack('C*',\Symfony\Component\String\b('ça éxagœrre')->toByteString('ISO-8859-15'));
$testraaz = unpack('C*',\Symfony\Component\String\u('ça éxaœèrre')->toByteString('utf-8'));
$testrza = unpack('C*','ça éxagèrre');
echo $test;
