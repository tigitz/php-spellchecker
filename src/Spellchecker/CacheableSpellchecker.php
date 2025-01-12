<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use ArrayIterator;
use PhpSpellcheck\Cache\CacheInterface;

final readonly class CacheableSpellchecker implements SpellcheckerInterface
{
    public function __construct(
        private SpellcheckerInterface $spellChecker,
        private CacheInterface $cache
    ) {}

    public function check(string $text, array $languages = [], array $context = []): iterable
    {
        $key = $this->generateCacheKey($text, $languages);

        $result = $this->cache->get($key);

        if ($result === null) {
            $result = $this->spellChecker->check($text, $languages, $context);

            $resultArray = iterator_to_array($result);
            $this->cache->set($key, $resultArray);

            return $result;
        }

        // @todo Convert array to iterable
       return $result;
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