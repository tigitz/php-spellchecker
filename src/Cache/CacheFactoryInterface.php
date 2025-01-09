<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use PhpSpellcheck\Cache\Stores\StoreInterface;

interface CacheFactoryInterface
{
    public static function create(?string $name = null, array $storeArgs = []): StoreInterface;
}