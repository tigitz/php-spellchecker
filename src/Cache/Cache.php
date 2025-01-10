<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use PhpSpellcheck\Cache\Stores\StoreInterface;
use PhpSpellcheck\Exception\InvalidArgumentException;

class Cache implements CacheFactoryInterface
{
    /**
     * Get a cache store instance by driver.
     *
     * @param array<string, mixed> $config
     */
    public static function create(?string $driver = null, array $config = []): StoreInterface
    {
        $driver ??= self::getDefaultDriver();

        $class = self::resolveStoreClass($driver);

        return $class::create(...$config);
    }

    /**
     * Resolve the cache store class.
     */
    public static function resolveStoreClass(string $driver): string
    {
        $class = \sprintf('%s\%s\%s%s', __NAMESPACE__, 'Stores', ucfirst($driver), 'Store');

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Cache store [{$driver}] is not defined.");
        }

        return $class;
    }

    /**
     * Get the default cache driver name.
     */
    private static function getDefaultDriver(): string
    {
        return 'file';
    }
}
