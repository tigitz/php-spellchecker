<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PhpSpellcheck\Cache\Stores\FileStore;
use Symfony\Contracts\Cache\CacheInterface;

class FileStoreTest extends TestCase
{
    protected FileStore $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = $this->getFileStoreInstance();

        $this->store->clear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->store->clear();
    }

    public function testInstanceOfCacheInterface()
    {
        $this->assertInstanceOf(CacheInterface::class, $this->store);
    }

    public function testGetCallbackStoresAndReturnsValue()
    {
        $this->assertSame($this->store->get('key', fn () => 'value'), 'value');
    }

    public function testSetMethodStoresValue()
    {
        $this->store->set('key', 'value');

        $this->assertSame($this->store->get('key', fn () => 'default'), 'value');
    }

    public function testItReturnsNullWhenKeyDoesNotExist()
    {
        $this->assertNull($this->store->getItem('non-existent-key')->get());
    }

    public function testClearMethodRemovesAllItems()
    {
        $this->store->set('key1', 'value1');
        $this->store->set('key2', 'value2');

        $this->store->clear();

        $this->assertNull($this->store->getItem('key1')->get());
        $this->assertNull($this->store->getItem('key2')->get());
    }

    public function testDeleteMethodRemovesSpecificItem()
    {
        $this->store->set('key', 'value');
        $this->store->delete('key');

        $this->assertNull($this->store->getItem('key')->get());
    }

    public function testClearMethodReturnsBoolean()
    {
        $this->store->set('key', 'value');
        $this->assertTrue($this->store->clear());
    }

    protected function getFileStoreInstance(): CacheInterface
    {
        return FileStore::create(namespace: 'FileStoreTest');
    }
}
