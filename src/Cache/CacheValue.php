<?php

namespace PhpSpellcheck\Cache;

final class CacheValue
{
    public function __construct(public mixed $value, public ?int $expiresAt = null)
    {
        //
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt < time();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function serialize(): string
    {
        return serialize($this);
    }
}