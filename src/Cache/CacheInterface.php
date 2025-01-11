<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use Psr\SimpleCache\CacheInterface as PSRCacheInterface;

interface CacheInterface extends PSRCacheInterface
{
    public function getFilePath(string $key): string;
}
