<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use Psr\Cache\CacheItemPoolInterface;

final readonly class CacheableSpellchecker implements SpellcheckerInterface
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly SpellcheckerInterface $spellchecker
    ) {
    }

    public function check(
        string $text,
        array $languages = [],
        array $context = []
    ): iterable {
        $cacheKey = md5(serialize([$this->spellchecker, $text, $languages, $context]));

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            yield from $cacheItem->get();
            return;
        }

        $misspellings = iterator_to_array($this->spellchecker->check($text, $languages, $context));
        $this->cache->save($cacheItem->set($misspellings));

        yield from $misspellings;
    }

    public function getSupportedLanguages(): iterable
    {
        $cacheKey = md5(serialize([$this->spellchecker]));

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            yield from $cacheItem->get();
            return;
        }

        $languages = iterator_to_array($this->spellchecker->getSupportedLanguages());
        $this->cache->save($cacheItem->set($languages));

        yield from $languages;
    }
}