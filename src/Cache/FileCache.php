<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use Psr\Cache\CacheItemInterface;
use Composer\Autoload\ClassLoader;
use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Exception\InvalidArgumentException;

final class FileCache implements FileCacheInterface
{
    /**
     * @var array<string, CacheItemInterface>
     */
    private array $deferred = [];

    /**
     * $namespace - The namespace of the cache (e.g., 'Aspell' creates .phpspellcache.cache/Aspell/*)
     * $defaultLifetime - The default lifetime in seconds for cached items (0 = never expires)
     * $directory - Optional custom directory path for cache storage
     */
    public function __construct(
        private readonly string $namespace = '@',
        private readonly int $defaultLifetime = 0,
        private ?string $directory = null,
    ) {
        if ($directory === null) {
            $directory = $this->getDefaultDirectory();
        }

        $this->validateNamespace();

        $directory .= DIRECTORY_SEPARATOR . $namespace;

        if (!is_dir($directory) && !@mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory "%s" could not be created', $directory));
        }

        $this->directory = $directory .= DIRECTORY_SEPARATOR;
    }

    public static function create(
        string $namespace = '@',
        int $defaultLifetime = 0,
        ?string $directory = null
    ): self {
        return new self($namespace, $defaultLifetime, $directory);
    }

    public function getItem(string $key): CacheItemInterface
    {
        $this->validateKey($key);
        $filepath = $this->getFilePath($key);

        $item = new CacheItem($key);

        if (!file_exists($filepath)) {
            return $item;
        }

        $data = \PhpSpellcheck\file_get_contents($filepath);

        if ($data === '') {
            return $item;
        }

        $value = unserialize($data);

        if (! is_object($value)
            || ! property_exists($value, 'data')
            || ! property_exists($value, 'expiresAt')
        ) {
            return $item;
        }

        if ($value->expiresAt !== 0
            && $value->expiresAt !== null
            && $value->expiresAt <= time()
        ) {
            unlink($filepath);

            return $item;
        }

        $item->set($value->data)->setIsHit(true);

        if (is_int($value->expiresAt) && $value->expiresAt > 0) {
            $item->expiresAt(new \DateTime('@' . $value->expiresAt));
        }

        return $item;
    }

    /**
     * @param array<string> $keys
     * @return iterable<CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        return array_map(fn ($key): CacheItemInterface => $this->getItem($key), $keys);
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        $this->deferred = [];
        $files = glob($this->directory.'*');

        if ($files === false || empty($files)) {
            return false;
        }

        $result = true;
        foreach ($files as $file) {
            $result = unlink($file) && $result;
        }

        return $result;
    }

    public function deleteItem(string $key): bool
    {
        $this->validateKey($key);
        unset($this->deferred[$key]);

        if (!file_exists($this->getFilePath($key))) {
            return true;
        }

        return unlink($this->getFilePath($key));
    }

    public function deleteItems(array $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $this->deleteItem($key) && $result;
        }

        return $result;
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->validateKey($item->getKey());

        if (! property_exists($item, 'expiry')) {
            throw new InvalidArgumentException('CacheItem expiry property is required');
        }

        $expiresAt = match(true) {
            $item->expiry instanceof \DateTimeInterface => $item->expiry->getTimestamp(),
            $this->defaultLifetime > 0 => time() + $this->defaultLifetime,
            default => null
        };

        $value = (object) [
            'data' => $item->get(),
            'expiresAt' => $expiresAt,
        ];

        $serialized = serialize($value);
        $filepath = $this->getFilePath($item->getKey());

        try {
            return (bool) \PhpSpellcheck\file_put_contents($filepath, $serialized, LOCK_EX);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->validateKey($item->getKey());
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    public function commit(): bool
    {
        $success = true;
        foreach ($this->deferred as $item) {
            $success = $this->save($item) && $success;
        }
        $this->deferred = [];

        return $success;
    }

    private function getDefaultDirectory(): string
    {
        return dirname(array_keys(ClassLoader::getRegisteredLoaders())[0]).'/.phpspellcheck.cache';
    }

    public function getFilePath(string $key): string
    {
        return $this->directory . $key;
    }

    private function validateNamespace(): void
    {
        if (\PhpSpellcheck\preg_match('#[^-+_.A-Za-z0-9]#', $this->namespace, $match) === 1) {
            throw new InvalidArgumentException(sprintf('Namespace contains "%s" but only characters in [-+_.A-Za-z0-9] are allowed.', $match[0]));
        }
    }

    private function validateKey(string $key): void
    {
        if (\PhpSpellcheck\preg_match('/^[a-zA-Z0-9_\.]+$/', $key) === 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid cache key "%s". A cache key can only contain letters (a-z, A-Z), numbers (0-9), underscores (_), and periods (.).',
                    $key
                )
            );
        }
    }
}
