<?php

declare(strict_types=1);

namespace PhpSpellcheck\Cache;

use DateInterval;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

final class CacheItem implements CacheItemInterface
{
    public function __construct(
        private readonly string $key,
        private mixed $value = null,
        public ?DateTimeInterface $expiry = null,
        private bool $isHit = false
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiry = $expiration;

        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiry = null;

            return $this;
        }

        if (is_int($time)) {
            $this->expiry = new \DateTime('@' . (time() + $time));

            return $this;
        }

        $datetime = new \DateTime();
        $datetime->add($time);

        $this->expiry = $datetime;

        return $this;
    }

    public function setIsHit(bool $hit): void
    {
        $this->isHit = $hit;
    }
}