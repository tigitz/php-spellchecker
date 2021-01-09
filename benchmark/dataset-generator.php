<?php

use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\Spellchecker\Jamspell;
use PhpSpellcheck\Spellchecker\LanguageTool;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__.'/../vendor/autoload.php';

$corpus = [];
$handle = fopen(__DIR__.'/corpus/wikipedia.dat.txt', 'rb');
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        if(mb_strpos($buffer, '$') === 0) {
            $correctWord = str_replace('_', '', ltrim(trim($buffer), '$'));

            continue;
        }

        $corpus[str_replace('_', '', trim($buffer))] = $correctWord;
    }
    fclose($handle);
}
//$corpus = '';
//$handle = fopen(__DIR__.'/corpus/missp.dat.txt', 'rb');
//if ($handle) {
//    while (($buffer = fgets($handle, 4096)) !== false) {
//        if(mb_strpos($buffer, '$') === 0) {
////            $correctWord = str_replace('_', '', ltrim(trim($buffer), '$'));
//
//            continue;
//        }
//
//        $corpus .= str_replace('_', '', trim($buffer)).PHP_EOL;
//    }
//    fclose($handle);
//}

$ok = 0;
$notOk = 0;
foreach ($corpus as $error => $correctWord) {
    /** @var \PhpSpellcheck\Misspelling[] $misspellings */
//    $misspellings = iterator_to_array(
//        (new Aspell(new CommandLine(['docker','run','--rm', '-i', 'starefossen/aspell'])))
//    ->check(
//            $error,
//            ['en-US'],
//            ['ctx' => 'ctx']
//        )
//    );
    $misspellings = iterator_to_array(
        (
            new \PhpSpellcheck\Spellchecker\MultiSpellchecker([
            new Jamspell(new Psr18Client(), 'http://localhost:5466/candidates'),
            new \PhpSpellcheck\Spellchecker\PHPPspell(),
                    new LanguageTool(new LanguageToolApiClient('http://localhost:8011'))
                    ]
            )
        )->check(
            $error,
            ['en-US'],
            ['ctx' => 'ctx']
        )
    );



    if (empty($misspellings)) {
        $notOk++;
        continue;
    }

    if(in_array($correctWord, $misspellings[0]->getSuggestions())) {
        $ok++;
    } else {
        $notOk++;
    }

    echo ($ok+$notOk).'/'.\count($corpus).PHP_EOL.PHP_EOL;

    echo 'OK: '.$ok.PHP_EOL;
    echo 'WRONG: '.$notOk.PHP_EOL;
    echo 'SCORE: '.$ok*100/($ok+$notOk).'%';
}


