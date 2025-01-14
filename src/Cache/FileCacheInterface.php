<?php

namespace PhpSpellcheck\Cache;

use Psr\Cache\CacheItemPoolInterface;

interface FileCacheInterface extends CacheItemPoolInterface
{
    public function getFilePath(string $key): string;
}