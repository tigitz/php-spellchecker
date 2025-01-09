<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use PhpSpellcheck\Cache\Stores\StoreInterface;
use PhpSpellcheck\Exception\InvalidArgumentException;

class Cache implements CacheFactoryInterface
{
    /**
     * Get a cache store instance by name.
     *
     * @param array<string, mixed> $storeArgs
     */
    public static function create(?string $name = null, array $storeArgs = []): StoreInterface
    {
        $name = $name ?? self::getDefaultDriver();

        $class = self::resolveStoreClassName($name);

        return $class::create(...$storeArgs);
    }

    /**
     * Resolve the cache store class.
     */
    public static function resolveStoreClassName(string $name): string
    {
        $class = sprintf('%s\%s\%s%s', __NAMESPACE__, 'Stores', ucfirst($name), 'Store');

        if (! class_exists($class)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        return $class;
    }

    /**
     * Get the default cache driver name.
     */
    public static function getDefaultDriver(): string
    {
        return 'file';
    }
}