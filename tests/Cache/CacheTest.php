<?php

declare(strict_types=1);

use PhpSpellcheck\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\ItemInterface;
use PhpSpellcheck\Cache\Stores\StoreInterface;

class CacheTest extends TestCase
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

    public function testInstanceOfCacheInterface()
    {
        $this->assertInstanceOf(StoreInterface::class, $this->store);
    }

    public function testGetCallbackStoresAndReturnsValue()
    {
        $this->assertSame($this->store->get('key', fn () => 'value'), 'value');
    }

    public function testGetItemReturnsItemInterface()
    {
        $this->assertInstanceOf(ItemInterface::class, $this->store->getItem('key'));
    }

    public function testDeleteReturnsBoolean()
    {
        $this->store->get('key', fn () => 'value');
        $this->assertTrue($this->store->delete('key'));
    }

    public function testClearReturnsBoolean()
    {
        $this->store->get('key', fn () => 'value');
        $this->assertTrue($this->store->clear());
    }

    public function testInvalidStoreNameThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cache store [invalid] is not defined.');

        Cache::resolveStoreClass('invalid');
    }

    protected function getStoreInstance(): StoreInterface
    {
        return Cache::create(config: ['namespace' => 'CacheTest']);
    }
}
