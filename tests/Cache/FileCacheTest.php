<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PhpSpellcheck\Cache\FileCache;
use PhpSpellcheck\Cache\FileCacheInterface;

class FileCacheTest extends TestCase
{
    protected FileCacheInterface $cache;

    public function setUp(): void
    {
        $this->cache = new FileCache('FileCacheTest');
        $this->cache->clear();
    }

    public function tearDown(): void
    {
        $this->cache->clear();
    }

    public function testCreateReturnsFileCacheInstance(): void
    {
        $cache = FileCache::create('FileCacheTest');
        $this->assertInstanceOf(FileCache::class, $cache);
    }

    public function testGetItemReturnsNonExistentItem(): void
    {
        $item = $this->cache->getItem('key1');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testSaveAndGetItem(): void
    {
        $item = $this->cache->getItem('key2');
        $item->set('value2');

        $this->cache->save($item);

        $newItem = $this->cache->getItem('key2');

        $this->assertTrue($newItem->isHit());
        $this->assertEquals('value2', $newItem->get());
    }

    public function testDeleteItem(): void
    {
        $item = $this->cache->getItem('key3');
        $item->set('value3');
        $this->cache->save($item);

        $this->assertTrue($this->cache->deleteItem('key3'));
        $this->assertFalse($this->cache->hasItem('key3'));
    }

    public function testSaveDeferred(): void
    {
        $item = $this->cache->getItem('key4');
        $item->set('value4');

        $this->cache->saveDeferred($item);
        $this->assertFalse($this->cache->hasItem('key4'));

        $this->cache->commit();
        $this->assertTrue($this->cache->hasItem('key4'));
    }

    public function testClearCache(): void
    {
        $item1 = $this->cache->getItem('key5');
        $item1->set('value5');
        $this->cache->save($item1);

        $item2 = $this->cache->getItem('key6');
        $item2->set('value6');
        $this->cache->save($item2);

        $this->assertTrue($this->cache->clear());
        $this->assertFalse($this->cache->hasItem('key5'));
        $this->assertFalse($this->cache->hasItem('key6'));
    }

    public function testGetItems(): void
    {
        $keys = ['key7', 'key8'];
        $items = $this->cache->getItems($keys);

        foreach ($items as $item) {
            $this->assertFalse($item->isHit());
        }
    }

    public function testDeleteItems(): void
    {
        $item1 = $this->cache->getItem('key9');
        $item1->set('value9');
        $this->cache->save($item1);

        $item2 = $this->cache->getItem('key10');
        $item2->set('value10');
        $this->cache->save($item2);

        $this->assertTrue($this->cache->deleteItems(['key9', 'key10']));
        $this->assertFalse($this->cache->hasItem('key9'));
        $this->assertFalse($this->cache->hasItem('key10'));
    }

    public function testItemExpiration(): void
    {
        $item = $this->cache->getItem('expiring_key');
        $item->set('expiring_value');
        $item->expiresAt(new DateTime('+1 second'));
        $this->cache->save($item);

        $this->assertTrue($this->cache->hasItem('expiring_key'));
        sleep(2);
        $this->assertFalse($this->cache->getItem('expiring_key')->isHit());
    }

    public function testInvalidNamespaceThrowsException(): void
    {
        $this->expectException(\PhpSpellcheck\Exception\InvalidArgumentException::class);
        new FileCache('Invalid/Namespace');
    }

    public function testInvalidKeyThrowsException(): void
    {
        $this->expectException(\PhpSpellcheck\Exception\InvalidArgumentException::class);
        $this->cache->getItem('invalid/key');
    }

    public function testDefaultLifetime(): void
    {
        $cache = new FileCache('FileCacheTest', 1);
        $item = $cache->getItem('key');
        $item->set('value');
        $cache->save($item);

        $this->assertTrue($cache->hasItem('key'));
        sleep(2);
        $this->assertFalse($cache->getItem('key')->isHit());
    }

    public function testCustomDirectory(): void
    {
        $cache = new FileCache('FileCacheTest', 0, '/tmp');
        $item = $cache->getItem('key');
        $item->set('value');
        $cache->save($item);

        $this->assertFileExists('/tmp/FileCacheTest/key');
        $cache->clear();
    }

    public function testExpiredCachedFileIsDeletedWhenCallingGetItem(): void
    {
        $cache = new FileCache('FileCacheTest', 1, '/tmp');
        $item = $cache->getItem('unlinked_key');
        $item->set('value');
        $item->expiresAfter(1);
        $cache->save($item);

        sleep(2);

        $cache->getItem('unlinked_key');

        $this->assertFileDoesNotExist('/tmp/FileCacheTest/unlinked_key');
    }
}
