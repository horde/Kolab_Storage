<?php
/**
 * Test the Kolab folder handler.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Folder;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Exception;
use Horde_Kolab_Storage_List_Query_List;
use Horde_Kolab_Storage_Folder;
use Horde_Kolab_Storage_Folder_Base;
/**
 * Test the Kolab folder handler.
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
class BaseTest
extends TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(Horde_Kolab_Storage_Folder::class, $this->_getFolderMock());
    }

    public function testGetPath()
    {
        $this->assertEquals('path', $this->_getFolderMock()->getPath());
    }

    public function testGetNamespace()
    {
        $this->assertEquals(
            'personal',
            $this->_getFolderMock(array('namespace' => 'personal'))
            ->getNamespace()
        );
    }

    public function testMissingNamespace()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->getNamespace();
    }

    public function testGetTitle()
    {
        $this->assertEquals(
            'title',
            $this->_getFolderMock(array('name' => 'title'))->getTitle()
        );
    }

    public function testMissingTitle()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->getTitle();
    }

    public function testGetOwner()
    {
        $this->assertEquals(
            'owner',
            $this->_getFolderMock(array('owner' => 'owner'))->getOwner()
        );
    }

    public function testMissingOwner()
    {
        $this->assertNull($this->_getFolderMock()->getOwner());
    }

    public function testGetSubpath()
    {
        $this->assertEquals(
            'subpath',
            $this->_getFolderMock(array('subpath' => 'subpath'))->getSubpath()
        );
    }

    public function testMissingSubpath()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->getSubpath();
    }

    public function testGetDefault()
    {
        $this->assertTrue(
            $this->_getFolderMock(array('default' => true))->isDefault()
        );
    }

    public function testMissingDefault()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->isDefault();
    }

    public function testGetType()
    {
        $this->assertEquals(
            'type',
            $this->_getFolderMock(array('type' => 'type'))->getType()
        );
    }

    public function testMissingType()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->getType();
    }

    public function testGetPrefix()
    {
        $this->assertEquals(
            'prefix',
            $this->_getFolderMock(array('prefix' => 'prefix'))->getPrefix()
        );
    }

    public function testMissingPrefix()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getFolderMock()->getPrefix();
    }

    private function _getFolderMock($data = array())
    {
        return new Horde_Kolab_Storage_Folder_Base(
            $this->_getListMock($data),
            'path'
        );
    }

    private function _getListMock($data = array())
    {
        $query = $this->createMock(Horde_Kolab_Storage_List_Query_List::class);
        $query->expects($this->any())
            ->method('folderData')
            ->will($this->returnValue($data));
        return $query;
    }




    /**
     * Test renaming.
     */
    public function testSetName()
    {
        $this->markTestIncomplete('Currently broken');
        $GLOBALS['language'] = 'de_DE';
        $storage = $this->getMock('Horde_Kolab_Storage', array(), array(), '', false, false);
        $connection = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $connection->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(new Horde_Kolab_Storage_Driver_Namespace_Fixed()));
        $folder = new Horde_Kolab_Storage_Folder_Base('INBOX/Contacts');
        $folder->restore($storage, $connection);
        $folder->setName('TestAÖÜ');
        $this->assertEquals(Horde_String::convertCharset('INBOX/TestAÖÜ', 'UTF-8', 'UTF7-IMAP'), $folder->new_name);
    }

    /**
     * Test saving objects.
     */
    public function testSave()
    {
        $this->markTestIncomplete('Currently broken');
        $folder = $this->storage->getNewFolder();

        try {
            $result = $folder->save();
        } catch (Exception $e) {
            $this->assertEquals(Horde_Kolab_Storage_Exception::FOLDER_NAME_UNSET , $e->getCode());
        }
        $folder->setName('TestÄÖÜ');
        $result = $folder->exists();
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertFalse($result);
        $result = $folder->accessible();
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertFalse($result);

        $result = $folder->save();
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertTrue($result);
        $this->assertEquals("wrobel@example.org", $folder->getOwner());
        $this->assertEquals("TestÄÖÜ", $folder->getTitle());
        $this->assertEquals("mail", $folder->getType());
        $this->assertFalse($folder->isDefault());
        $this->assertTrue($folder->exists());
        $this->assertTrue($folder->accessible());

        $folder2 = $this->storage->getNewFolder();
        $folder2->setName('TestEvents');
        $attributes = array(
            'type' => 'event',
            'default' => true,
        );
        $result = $folder2->save($attributes);
        $this->assertTrue($result);
        $this->assertEquals("wrobel@example.org", $folder2->getOwner());
        $this->assertEquals("TestEvents", $folder2->getTitle());
        $this->assertEquals("event", $folder2->getType());
        $this->assertTrue($folder2->isDefault());

        $attributes = array(
            'default' => false,
            'dummy'   =>'test',
            'desc'   =>'A test folder',
        );
        $result = $folder2->save($attributes);
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertTrue($result);
        $this->assertEquals('test', $folder2->getAttribute('dummy'));
        $this->assertEquals('A test folder', $folder2->getAttribute('desc'));

        $folder2->setName('TestEventsNew');
        $result = $folder2->save($attributes);
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertTrue($result);

        $result = $folder->delete();
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertTrue($result);
        $result = $folder2->delete();
        if (is_a($result, 'PEAR_Error')) {
            $this->assertEquals('', $result->message);
        }
        $this->assertTrue($result);
    }

    /**
     * Test class construction with missing configuration data.
     */
    public function testGetImapFailNoServer()
    {
        $this->markTestIncomplete('Currently broken');
        $session = Horde_Kolab_Session::singleton('anonymous', null, true);
        $imap = $session->getImapParams();
        $this->assertEquals('localhost', $imap['hostspec']);
    }

    /**
     * Test triggering.
     */
    public function testTriggerOwn()
    {
        $this->markTestIncomplete('Currently broken');
        $folder = $this->getMock('Horde_Kolab_Storage_Folder', array('triggerUrl'));
        $folder->expects($this->once())
            ->method('triggerUrl')
            ->with($this->equalTo('https://fb.example.org/freebusy/trigger/wrobel@example.org/Kalender.pfb'));

        $connection = $this->storage->getConnection();
        $folder->restore($this->storage, $connection->connection);
        $folder->setName('Kalender');
        $folder->save(array('type' => 'event'));
    }

    /**
     * Test triggering.
     */
    public function testTriggerForeign()
    {
        $this->markTestIncomplete('Currently broken');
        $folder = $this->getMock(Horde_Kolab_Storage_Folder::class, array('triggerUrl'));
        $folder->expects($this->exactly(2))
            ->method('triggerUrl')
            ->with($this->equalTo('https://fb.example.org/freebusy/trigger/test@example.org/Kalender.pfb'));

        $connection = $this->storage->getConnection();
        $folder->restore($this->storage, $connection->connection);
        $folder->setName('user/test/Kalender');
        $folder->save(array('type' => 'event'));
    }
}
