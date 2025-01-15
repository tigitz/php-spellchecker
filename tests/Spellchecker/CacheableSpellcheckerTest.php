<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PhpSpellcheck\Cache\FileCache;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\Cache\FileCacheInterface;
use PhpSpellcheck\Spellchecker\CacheableSpellchecker;

class CacheableSpellcheckerTest extends TestCase
{
    private const FAKE_BINARIES_PATH = __DIR__ . '/../Fixtures/Aspell/bin/aspell.sh';

    protected FileCacheInterface $cache;

    protected CacheableSpellchecker $cacheableSpellchecker;

    public function setUp(): void
    {
        $this->cache = new FileCache('CacheableSpellcheckerTest');
        $this->cache->clear();

        $spellchecker = Aspell::create(self::FAKE_BINARIES_PATH);
        $this->cacheableSpellchecker = new CacheableSpellchecker($this->cache, $spellchecker);
    }

    public function tearDown(): void
    {
        $this->cache->clear();
    }

    public function testCheckReturnsFromCache(): void
    {
        $text = 'testt speling';
        $result1 = iterator_to_array($this->cacheableSpellchecker->check($text));
        $result2 = iterator_to_array($this->cacheableSpellchecker->check($text));

        $this->assertEquals($result1, $result2);
    }

    public function testGetSupportedLanguagesReturnsFromCache(): void
    {
        $langs1 = iterator_to_array($this->cacheableSpellchecker->getSupportedLanguages());
        $langs2 = iterator_to_array($this->cacheableSpellchecker->getSupportedLanguages());

        $this->assertSame($langs1, $langs2);
    }

    public function testCheckWithDifferentParameters(): void
    {
        $text = 'testt speling';
        $result1 = iterator_to_array($this->cacheableSpellchecker->check($text, ['en_US']));
        $result2 = iterator_to_array($this->cacheableSpellchecker->check($text, ['en_GB']));

        foreach ($result1 as $misspelling) {
            $this->assertNotSame($misspelling, $result2);
        }
    }
}
