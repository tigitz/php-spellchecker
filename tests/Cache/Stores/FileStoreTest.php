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

    protected function getFileStoreInstance(): CacheInterface
    {
        return FileStore::create(namespace: 'FileStoreTest');
    }

    public function testInstanceOfCacheInterface()
    {
        $this->assertInstanceOf(CacheInterface::class, $this->store);
    }

    public function testGetCallbackStoresAndReturnsValue()
    {
        $this->assertSame($this->store->get('key', fn () => 'value'), 'value');
    }
}