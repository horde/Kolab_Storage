<?php
/**
 * Test the cached data handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Data;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Cache;
use Horde_Kolab_Storage_Exception;
use Horde_Kolab_Storage_Queriable;
use Horde_Cache;
use Horde_Cache_Storage_Mock;
/**
 * Test the cached data handler.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class CachedTest
extends TestCase
{
    public function testDefaultType()
    {
        $this->assertEquals(
            'event',
            $this->_getDataCache()->getType()
        );
    }

    public function testStamp()
    {
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Stamp',
            $this->_getDataCache()->getStamp()
        );

    }

    public function testFetchPart()
    {
        $part = stream_get_contents(
            $this->_getDataCache()
            ->fetchPart(1, '2')
        );
        $this->assertStringContainsString('<event', $part);
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testGetMissingObjects()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->getMockDataCache()->getObjects();
    }

    public function testSynchronize()
    {
        // Still fishy
        $this->assertNull($this->_getDataCache()->synchronize());
    }

    public function testFetch()
    {
        $objects = $this->_getDataCache()
            ->fetch(array(1, 2, 4));
        $this->assertEquals('libkcal-543769073.130', $objects[4]['uid']);
    }

    public function testDataQueriable()
    {
        $data = $this->_getDataCache();
        $this->assertTrue($data instanceof Horde_Kolab_Storage_Queriable);
    }

    public function testGetObjects()
    {
        $this->assertIsArray(
            $this->_getDataCache()
            ->getObjects()
        );
    }

    public function testObjects()
    {
        $objects = $this->_getDataCache()
            ->getObjects();
        $this->assertEquals(
            'libkcal-543769073.130',
            $objects['libkcal-543769073.130']['uid']
        );
    }

    public function testGetObjectIds()
    {
        $this->assertIsArray(
            $this->_getDataCache()->getObjectIds()
        );
    }

    public function testObjectIds()
    {
        $this->assertEquals(
            array(
                'libkcal-543769073.132',
                'libkcal-543769073.131',
                'libkcal-543769073.130'
            ),
            $this->_getDataCache()->getObjectIds()
        );
    }

    public function testBackendId()
    {
        $this->assertEquals(
            '1',
            $this->_getDataCache()
            ->getBackendId('libkcal-543769073.132')
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testMissingBackendId()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getDataCache()
            ->getBackendId('NOSUCHOBJECT');
    }

    public function testExists()
    {
        $this->assertTrue(
            $this->_getDataCache()
            ->objectIdExists('libkcal-543769073.132')
        );
    }

    public function testDoesNotExist()
    {
        $this->assertFalse(
            $this->_getDataCache()
            ->objectIdExists('NOSUCHOBJECT')
        );
    }

    public function testGetObject()
    {
        $object = $this->_getDataCache()
            ->getObject('libkcal-543769073.130');
        $this->assertEquals(
            'libkcal-543769073.130',
            $object['uid']
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testGetMissingObject()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $object = $this->_getDataCache()
            ->getObject('NOSUCHOBJECT');
    }

    public function testModify()
    {
        $store = $this->getMessageStorage(
            array(
                'cache' => new Horde_Cache(new Horde_Cache_Storage_Mock())
            )
        );
        $data = $store->getData('INBOX/Notes');
        $object = array('summary' => 'test', 'uid' => 'UID');
        $obid = $data->create($object);
        $storage_objects = $data->fetch(array($obid));
        $storage_objects[$obid]->setData(array('summary' => 'modified', 'uid' => 'UID'));
        $data->modify($storage_objects[$obid]);
        $object = $data->getObject('UID');
        $this->assertEquals('modified', $object['summary']);
    }
     

    private function _getDataCache()
    {
        $this->storage = $this->getMessageStorage(
            array(
                'cache' => new Horde_Cache(new Horde_Cache_Storage_Mock())
            )
        );
        $cache = $this->storage->getData('INBOX/Calendar');
        $cache->synchronize();
        return $cache;
    }
}
