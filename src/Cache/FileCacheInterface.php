<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use Psr\Cache\CacheItemPoolInterface;

interface FileCacheInterface extends CacheItemPoolInterface
{
    public function getFilePath(string $key): string;
}
