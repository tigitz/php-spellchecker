<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$dependencies = [
    'LANGUAGETOOLS_ENDPOINT',
    'JAMSPELL_ENDPOINT'
];

foreach ($dependencies as $dependency) {
    echo sprintf('Waiting "%s" dependency'.PHP_EOL, $dependency);

    for (; ;) {
        $url = parse_url(getenv($dependency));
        if ($socket = @fsockopen($url['host'], $url['port'])) {
            echo sprintf('"%s" dependency is up'.PHP_EOL, $dependency).PHP_EOL;
            fclose($socket);

            break;
        }
        time_nanosleep(0, 100000000);
    }
}
