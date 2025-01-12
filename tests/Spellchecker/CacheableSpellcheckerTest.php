<?php

declare(strict_types=1);

use PhpSpellcheck\Cache\FileCache;
use PHPUnit\Framework\TestCase;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\Spellchecker\CacheableSpellchecker;

class CacheableSpellcheckerTest extends TestCase
{
    private const FAKE_BINARIES_PATH = __DIR__ . '/../Fixtures/Aspell/bin/aspell.sh';

    public function testAspellSpellcheck(): void
    {
        $checker = new CacheableSpellchecker(
            Aspell::create(self::FAKE_BINARIES_PATH),
            FileCache::create('CacheableSpellcheckerTest')
        );

        $result = $checker->check('hello');

        $this->assertCount(6, iterator_to_array($result));
    }
}