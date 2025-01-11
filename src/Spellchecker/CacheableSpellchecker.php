<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use ArrayObject;
use PhpSpellcheck\Cache\CacheInterface;

final class CacheableSpellchecker implements SpellcheckerInterface
{
    public function __construct(
        private readonly SpellcheckerInterface $spellChecker,
        private readonly CacheInterface $cache
    ) {}

    /**
     * @return iterable<\PhpSpellcheck\MisspellingInterface>
     */
    public function check(string $text, array $languages = [], array $context = []): iterable
    {
        $key = $this->generateCacheKey($text, $languages);

        $result = $this->cache->get($key);

        if ($result === null) {
            $result = $this->spellChecker->check($text, $languages, $context);
            $this->cache->set($key, $result);

            return $result;
        }

        return (new ArrayObject((array) $result))->getIterator();
    }

    /**
     * @param array<string> $languages
     */
    private function generateCacheKey(string $text, array $languages = []): string
    {
        return md5(sprintf('%s_%s', $text, implode('_', $languages)));
    }

    public function getSupportedLanguages(): iterable
    {
        return $this->spellChecker->getSupportedLanguages();
    }
}