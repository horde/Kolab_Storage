<?php
/**
 * Test the Kolab cache.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Cache;
use Horde_Cache_Storage_Mock;
use Horde_Kolab_Storage_Cache;
use Horde_Kolab_Storage_Exception;

/**
 * Test the Kolab cache.
 *
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class CacheTest
extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->horde_cache = new Horde_Cache(
            new Horde_Cache_Storage_Mock()
        );
        $this->cache = new Horde_Kolab_Storage_Cache($this->horde_cache);
    }

    public function testGetDataCache()
    {
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Cache_Data',
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testCachedDataCache()
    {
        $this->assertSame(
            $this->cache->getDataCache($this->_getDataParameters()),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testNewHostNewData()
    {
        $params = $this->_getDataParameters();
        $params['host'] = 'b';
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testDataMissingHost()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $params = $this->_getDataParameters();
        unset($params['host']);
        $this->cache->getDataCache($params);
    }

    public function testNewPortNewData()
    {
        $params = $this->_getDataParameters();
        $params['port'] = 2;
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testDataMissingPort()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $params = $this->_getDataParameters();
        unset($params['port']);
        $this->cache->getDataCache($params);
    }

    public function testNewFolderNewData()
    {
        $params = $this->_getDataParameters();
        $params['folder'] = 'J';
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testDataMissingFolder()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $params = $this->_getDataParameters();
        unset($params['folder']);
        $this->cache->getDataCache($params);
    }

    public function testNewTypeNewData()
    {
        $params = $this->_getDataParameters();
        $params['type'] = 'f';
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testDataMissingType()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $params = $this->_getDataParameters();
        unset($params['type']);
        $this->cache->getDataCache($params);
    }

    public function testNewOwnerNewData()
    {
        $params = $this->_getDataParameters();
        $params['owner'] = 'f';
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($this->_getDataParameters())
        );
    }

    public function testKeyCollision()
    {
        $params2 = $this->_getDataParameters();
        $params2['folder'] = 'I';
        $params2['type'] = 'e/';
        $params = $this->_getDataParameters();
        $params['folder'] = 'I/e';
        $params['type'] = '';
        $this->assertNotSame(
            $this->cache->getDataCache($params),
            $this->cache->getDataCache($params2)
        );
    }

    public function testDataMissingOwner()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $params = $this->_getDataParameters();
        unset($params['owner']);
        $this->cache->getDataCache($params);
    }

    public function testLoadData()
    {
        $this->assertFalse(
            $this->cache->loadData('test')
        );
    }

    public function testStoreData()
    {
        $this->cache->storeData('test', true);
        $this->assertTrue(
            $this->cache->loadData('test')
        );
    }

    public function testLoadAttachment()
    {
        $this->assertFalse(
            $this->cache->loadAttachment('test', '1', '1')
        );
    }

    public function testStoreAttachment()
    {
        $this->cache->storeAttachment('test', '1', '1', $this->_getResource());
        $this->assertEquals(
            'test',
            stream_get_contents(
                $this->cache->loadAttachment('test', '1', '1')
            )
        );
    }

    public function testStoreSameAttachment()
    {
        $resource = $this->_getResource();
        $resource2 = $this->_getResource();
        $this->cache->storeAttachment('test', '1', '1', $resource);
        $this->cache->storeAttachment('test', '1', '1', $resource2);
        rewind($resource);
        rewind($resource2);
        $this->assertSame(
            stream_get_contents($resource2),
            stream_get_contents($this->cache->loadAttachment('test', '1', '1'))
        );
        $this->assertSame(
            stream_get_contents($resource),
            stream_get_contents($this->cache->loadAttachment('test', '1', '1'))
        );
    }

    public function testStoreDifferentUidAttachment()
    {
        $resource = $this->_getResource();
        $resource2 = $this->_getResource();
        $this->cache->storeAttachment('test', '1', '1', $resource);
        $this->cache->storeAttachment('test', '2', '1', $resource2);
        rewind($resource);
        rewind($resource2);
        $this->assertSame(
            stream_get_contents($resource),
            stream_get_contents($this->cache->loadAttachment('test', '1', '1'))
        );
        $this->assertSame(
            stream_get_contents($resource2),
            stream_get_contents($this->cache->loadAttachment('test', '2', '1'))
        );
    }

    public function testStoreDifferentAttachmentId()
    {
        $resource = $this->_getResource();
        $resource2 = $this->_getResource();
        $this->cache->storeAttachment('test', '1', '1', $resource);
        $this->cache->storeAttachment('test', '1', '2', $resource2);
        rewind($resource);
        rewind($resource2);
        $this->assertSame(
            stream_get_contents($resource),
            stream_get_contents($this->cache->loadAttachment('test', '1', '1'))
        );
        $this->assertSame(
            stream_get_contents($resource2),
            stream_get_contents($this->cache->loadAttachment('test', '1', '2'))
        );
    }

    public function testLoadList()
    {
        $this->assertFalse(
            $this->cache->loadList('test')
        );
    }

    public function testStoreList()
    {
        $this->cache->storeList('test', true);
        $this->assertTrue(
            $this->cache->loadList('test')
        );
    }

    public function testCachingListData()
    {
        $this->cache->storeList('user@example.com:143', array('folders' => array('a', 'b')));
        $this->assertEquals(array('folders' => array('a', 'b')), $this->cache->loadList('user@example.com:143'));
    }

    private function _getIdParameters()
    {
        return array('host' => 'a', 'port' => 1, 'user' => 'x');
    }

    private function _getDataParameters()
    {
        return array(
            'host' => 'a',
            'port' => 1,
            'prefix' => 'P',
            'folder' => 'I',
            'type' => 'e',
            'owner' => 'x',
        );
    }

    private function _getResource()
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, 'test');
        rewind($resource);
        return $resource;
    }
}
