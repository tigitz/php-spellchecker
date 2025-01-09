<?php

declare(strict_types=1);

use PhpSpellcheck\Cache\Cache;
use PhpSpellcheck\Cache\Stores\StoreInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class CacheManagerTest extends TestCase
{
    protected StoreInterface $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = $this->getStoreInstance();

        $this->store->clear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->store->clear();
    }

    protected function getStoreInstance(): CacheInterface
    {
        return Cache::create(
            namespace: 'CacheManagerTest',
            cacheDirectory: __DIR__.'/../../.phpspellcheck.cache'
        );
    }

    public function testInstanceOfCacheInterface()
    {
        $this->assertInstanceOf(StoreInterface::class, $this->store);
    }
}