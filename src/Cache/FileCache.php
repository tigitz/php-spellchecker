<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use Composer\Autoload\ClassLoader;
use PhpSpellcheck\Exception\InvalidArgumentException;

final class FileCache implements CacheInterface
{
    public function __construct(
        private readonly string $namespace = '',
        private readonly int $defaultLifetime = 0,
        private ?string $directory = null,
    ) {
        if ($directory === null) {
            $directory = $this->getDefaultDirectory();
        }

        if (strlen($namespace) > 0) {
            $this->validateNamespace($namespace);
            $directory .= DIRECTORY_SEPARATOR . $namespace;
        } else {
            $directory .= DIRECTORY_SEPARATOR . '@';
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->directory = $directory .= DIRECTORY_SEPARATOR;
    }

    public static function create(string $namespace = '', int $defaultLifetime = 0, ?string $directory = null): CacheInterface
    {
        return new self($namespace, $defaultLifetime, $directory);
    }

    public function getDefaultDirectory(): string
    {
        return dirname(array_keys(ClassLoader::getRegisteredLoaders())[0]).'/.phpspellcheck.cache';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->getValueObject($key)?->value;
    }

    private function getValueObject(string $key): ?CacheValue
    {
        try {
            $value = unserialize(\PhpSpellcheck\file_get_contents($this->getFilePath($key)));

            return $value instanceof CacheValue ? $value : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function has(string $key): bool
    {
        if (!file_exists($this->getFilePath($key))) {
            return false;
        }

        $object = $this->getValueObject($key);

        return $object !== null && $object->isValid();
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->validateKey($key);

        $ttl ??= $this->defaultLifetime;

        if ($ttl instanceof \DateInterval) {
            $expiresAt = (new \DateTime())->add($ttl)->getTimestamp();
        } else {
            $expiresAt = $ttl > 0 ? time() + $ttl : null;
        }

        $data = new CacheValue($value, $expiresAt);

        return (bool) \PhpSpellcheck\file_put_contents($this->getFilePath($key), $data->serialize(), LOCK_EX);
    }

    public function delete(string $key): bool
    {
        if (!$this->has($key)) {
            return false;
        }

        return unlink($this->getFilePath($key));
    }

    public function clear(): bool
    {
        $files = glob($this->directory.'*');

        if ($files === false || empty($files)) {
            return false;
        }

        $result = true;
        foreach ($files as $file) {
            $result = unlink($file);
        }

        return $result;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    /**
     * @param iterable<mixed> $values
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $result = $this->set($key, $value, $ttl) && $result;
            }
        }

        return $result;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $this->delete($key) && $result;
        }

        return $result;
    }

    public function getFilePath(string $key): string
    {
        return $this->directory . $key;
    }

    private function validateNamespace(string $namespace): void
    {
        if (\PhpSpellcheck\preg_match('#[^-+_.A-Za-z0-9]#', $namespace, $match) === 1) {
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
