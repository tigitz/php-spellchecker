<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache\Stores;

use Symfony\Contracts\Cache\CacheInterface;

interface StoreInterface extends CacheInterface
{
    public static function create(string $namespace = '', int $defaultLifetime = 3600, ?string $cacheDirectory = null): self;

    public function clear(string $prefix = ''): bool;
}