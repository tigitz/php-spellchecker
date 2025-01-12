<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PhpSpellcheck\Cache\FileCache;
use PhpSpellcheck\Cache\CacheInterface;

class FileCacheTest extends TestCase
{
    protected CacheInterface $cache;

    public function setUp(): void
    {
        $this->cache = new FileCache('FileCacheTest');
        $this->cache->clear();
    }

    public function tearDown(): void
    {
        $this->cache->clear();
    }

    public function testGetReturnsNullWhenNotSet(): void
    {
        $this->assertNull($this->cache->get('foo'));
    }

    public function testGetReturnsValueWhenSet(): void
    {
        $this->cache->set('foo', 'bar');

        $this->assertSame('bar', $this->cache->get('foo'));
    }

    public function testGetReturnsDefaultWhenNotSet(): void
    {
        $this->assertSame('bar', $this->cache->get('foo', 'bar'));
    }

    public function testCacheWithLifetime(): void
    {
        $this->cache->set('foo', 'bar', 1);

        $this->assertSame('bar', $this->cache->get('foo', 'baz'));

        sleep(2);

        $this->assertSame('baz', $this->cache->get('foo', 'baz'));
    }

    public function testHas(): void
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->cache->set('foo', 'bar');

        $this->assertTrue($this->cache->has('foo'));
    }

    public function testDelete(): void
    {
        $this->cache->set('foo', 'bar');

        $this->assertTrue($this->cache->has('foo'));

        $this->cache->delete('foo');

        $this->assertFalse($this->cache->has('foo'));
    }

    public function testClear(): void
    {
        $this->cache->set('foo', 'bar');
        $this->cache->set('baz', 'qux');

        $this->assertTrue($this->cache->has('foo'));
        $this->assertTrue($this->cache->has('baz'));

        $this->cache->clear();

        $this->assertFalse($this->cache->has('foo'));
        $this->assertFalse($this->cache->has('baz'));
    }

    public function testGetMultiple(): void
    {
        $this->cache->set('foo', 'bar');
        $this->cache->set('baz', 'qux');

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], iterator_to_array($this->cache->getMultiple(['foo', 'baz'])));
    }

    public function testSetMultiple(): void
    {
        $this->assertTrue($this->cache->setMultiple(['foo' => 'bar', 'baz' => 'qux']));

        $this->assertSame('bar', $this->cache->get('foo'));
        $this->assertSame('qux', $this->cache->get('baz'));
    }

    public function testDeleteMultiple(): void
    {
        $this->cache->set('foo', 'bar');
        $this->cache->set('baz', 'qux');

        $this->assertTrue($this->cache->deleteMultiple(['foo', 'baz']));

        $this->assertFalse($this->cache->has('foo'));
        $this->assertFalse($this->cache->has('baz'));
    }

    public function testSetMultipleWithTtl(): void
    {
        $this->assertTrue($this->cache->setMultiple(['foo' => 'bar', 'baz' => 'qux'], 1));

        $this->assertSame('bar', $this->cache->get('foo'));
        $this->assertSame('qux', $this->cache->get('baz'));

        sleep(2);

        $this->assertNull($this->cache->get('foo'));
        $this->assertNull($this->cache->get('baz'));
    }

    public function testSetMultipleWithDateInterval(): void
    {
        $this->assertTrue($this->cache->setMultiple(['foo' => 'bar', 'baz' => 'qux'], new DateInterval('PT1S')));

        $this->assertSame('bar', $this->cache->get('foo'));
        $this->assertSame('qux', $this->cache->get('baz'));

        sleep(2);

        $this->assertNull($this->cache->get('foo'));
        $this->assertNull($this->cache->get('baz'));
    }

    public function testThrowsExceptionOnInvalidNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FileCache('InvalidNamespace/WithSlash');
    }

    public function testThrowsExceptionOnInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache->set('InvalidKey/WithSlash', 'bar');
    }

    public function testThrowsExceptionOnInvalidKeyInSetMultiple(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache->setMultiple(['InvalidKey/WithSlash' => 'bar']);
    }

    public function testCachesInvalidCharactersPassesWithMd5(): void
    {
        $key = md5('InvalidKey/WithSlash');

        $this->cache->set($key, 'bar');

        $this->assertSame('bar', $this->cache->get($key));
    }
}