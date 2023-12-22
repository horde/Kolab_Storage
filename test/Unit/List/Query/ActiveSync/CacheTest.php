<?php
/**
 * Test the handling of cached active sync data.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Query\ActiveSync;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_List_Query_ActiveSync;
use Horde_Kolab_Storage_List_Query_ActiveSync_Cache;
use Horde_Kolab_Storage_List_Cache;
/**
 * Test the handling of cached active sync data.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
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
    public function testInitActiveSync()
    {
        $this->query = $this->createMock(Horde_Kolab_Storage_List_Query_ActiveSync::class);
        $this->cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $this->cache->expects($this->once())
            ->method('hasQuery')
            ->with(Horde_Kolab_Storage_List_Query_ActiveSync_Cache::ACTIVE_SYNC)
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('getQuery')
            ->with(Horde_Kolab_Storage_List_Query_ActiveSync_Cache::ACTIVE_SYNC)
            ->will(
                $this->returnValue(
                    array('INBOX' => array('x' => 'y'))
                )
            );
        $this->query->expects($this->never())
            ->method('getActiveSync');
        $activesync = new Horde_Kolab_Storage_List_Query_ActiveSync_Cache(
            $this->query, $this->cache
        );
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    public function testGetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->once())
            ->method('getActiveSync')
            ->with('INBOX')
            ->will($this->returnValue(array('x' => 'y')));
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    public function testCachedGetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->once())
            ->method('getActiveSync')
            ->with('INBOX')
            ->will($this->returnValue(array('x' => 'y')));
        $activesync->getActiveSync('INBOX');
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    public function testStoredGetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->once())
            ->method('getActiveSync')
            ->with('INBOX')
            ->will($this->returnValue(array('x' => 'y')));
        $this->cache->expects($this->once())
            ->method('setQuery')
            ->with(
                Horde_Kolab_Storage_List_Query_ActiveSync_Cache::ACTIVE_SYNC,
                array('INBOX' => array('x' => 'y'))
            );
        $this->cache->expects($this->once())
            ->method('save');
        $activesync->getActiveSync('INBOX');
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }


    public function testSetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->once())
            ->method('setActiveSync')
            ->with('INBOX', array('x' => 'y'));
        $activesync->setActiveSync('INBOX', array('x' => 'y'));
    }

    public function testCacheSetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->cache->expects($this->once())
            ->method('setQuery')
            ->with(
                Horde_Kolab_Storage_List_Query_ActiveSync_Cache::ACTIVE_SYNC,
                array('INBOX' => array('x' => 'y'))
            );
        $this->cache->expects($this->once())
            ->method('save');
        $activesync->setActiveSync('INBOX', array('x' => 'y'));
    }

    public function testSetGetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $value = array('FOO' => 'BAR');
        $activesync->setActiveSync('INBOX', $value);
        $this->assertEquals($value, $activesync->getActiveSync('INBOX'));
    }

    public function testUpdateAfterCreateFolder()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->never())
            ->method('getActiveSync');
        $activesync->updateAfterCreateFolder('INBOX');
    }

    public function testUpdateAfterDeleteFolder()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->exactly(2))
            ->method('getActiveSync')
            ->with('INBOX')
            ->will($this->returnValue(array('x' => 'y')));
        $activesync->getActiveSync('INBOX');
        $activesync->updateAfterDeleteFolder('INBOX');
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    public function testUpdateAfterRenameFolder()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->once())
            ->method('getActiveSync')
            ->with('FOO')
            ->will($this->returnValue(array('x' => 'y')));
        $activesync->getActiveSync('FOO');
        $activesync->updateAfterRenameFolder('FOO', 'BAR');
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('BAR'));
    }

    public function testSynchronize()
    {
        $activesync = $this->_getActivesync();
        $this->query->expects($this->exactly(2))
            ->method('getActiveSync')
            ->with('INBOX')
            ->will($this->returnValue(array('x' => 'y')));
        $activesync->getActiveSync('INBOX');
        $activesync->synchronize();
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    private function _getActivesync()
    {
        $this->query = $this->createMock(Horde_Kolab_Storage_List_Query_ActiveSync::class);
        $this->cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        return new Horde_Kolab_Storage_List_Query_ActiveSync_Cache(
            $this->query, $this->cache
        );
    }
}
