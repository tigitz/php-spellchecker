<?php

declare(strict_types=1);

namespace PhpSpellcheck\Traits;

use PhpSpellcheck\Cache\Cache;
use PhpSpellcheck\Cache\Stores\StoreInterface;

trait SpellcheckerCacheTrait
{
    /**
     * The cache store instance.
     */
    private StoreInterface $cache;

    /**
     * The cache store instance.
     *
     * @param array<string, mixed> $config
     */
    private function initCache(array $config = []): void
    {
        $config['namespace'] ??= $this->getCacheNamespace();

        $this->cache = Cache::create(config: $config);
    }

    /**
     * Get the cache key for the given text and languages.
     *
     * @param array<int, string> $languages
     */
    private function getCacheKey(string $text, array $languages): string
    {
        return md5(sprintf('%s_%s', $text, implode('_', $languages)));
    }

    /**
     * Get the cache namespace.
     */
    private function getCacheNamespace(): string
    {
        $parts = explode('\\', get_class($this));

        return end($parts);
    }
}
