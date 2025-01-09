<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache\Stores;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

interface StoreInterface extends CacheInterface
{
    /**
     * Clear data from cache.
     */
    public function clear(string $prefix = ''): bool;

    /**
     * Fetches an item from the cache.
     */
    public function getItem(string $key): ItemInterface;

    /**
     * Create a new cache store instance.
     */
    public static function create(string $namespace = '', int $defaultLifetime = 3600, ?string $cacheDirectory = null): self;
}