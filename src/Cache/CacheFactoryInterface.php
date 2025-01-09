<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use PhpSpellcheck\Cache\Stores\StoreInterface;

interface CacheFactoryInterface
{
    public static function create(?string $name = null, string $namespace = '', int $defaultLifetime = 3600, ?string $cacheDirectory = null): StoreInterface;
}