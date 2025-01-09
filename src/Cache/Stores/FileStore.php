<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache\Stores;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FileStore implements StoreInterface
{
    /**
     * The filesystem adapter instance.
     */
    public function __construct(private FilesystemAdapter $filesystemAdapter)
    {
        //
    }

    /**
     * Create a new file cache store instance.
     */
    public static function create(string $namespace = '', int $defaultLifetime = 3600, ?string $cacheDirectory = null): self
    {
        return new self(new FilesystemAdapter($namespace, $defaultLifetime, $cacheDirectory ?? dirname(__DIR__, 5).'/..cache'));
    }

    /**
     * Fetches an item from the cache.
     */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null): mixed
    {
        return $this->filesystemAdapter->get($key, $callback, $beta, $metadata);
    }

    /**
     * Removed an item from the pool.
     */
    public function delete(string $key): bool
    {
        return $this->filesystemAdapter->delete($key);
    }

    /**
     * Fetches an item from the cache.
     */
    public function getItem(string $key): ItemInterface
    {
        return $this->filesystemAdapter->getItem($key);
    }

    /**
     * Clear data from cache.
     */
    public function clear(string $prefix = ''): bool
    {
        return $this->filesystemAdapter->clear($prefix);
    }
}