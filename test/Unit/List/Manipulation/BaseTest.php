<?php
/**
 * Test the operations of the list manipulator.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Manipulation;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_List_Manipulation_Base;
use Horde_Kolab_Storage_List_Manipulation_Listener;

/**
 * Test the operations of the list modifier.
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
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
class BaseTest
extends TestCase
{
    public function testCreateFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('create')
            ->with('TEST');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $manipulation->createFolder('TEST');
    }

    public function testCreateFolderWithType()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('create')
            ->with('TEST');
        $driver->expects($this->once())
            ->method('setAnnotation')
            ->with('TEST', '/shared/vendor/kolab/folder-type', 'event');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $manipulation->createFolder('TEST', 'event');
    }

    public function testDeleteFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('delete')
            ->with('TEST');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $manipulation->deleteFolder('TEST');
    }

    public function testRenameFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('rename')
            ->with('FOO', 'BAR');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $manipulation->renameFolder('FOO', 'BAR');
    }

    public function testUpdateAfterCreateFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $listener = $this->createMock(Horde_Kolab_Storage_List_Manipulation_Listener::class);
        $listener->expects($this->once())
            ->method('updateAfterCreateFolder')
            ->with('TEST');
        $manipulation->registerListener($listener);
        $manipulation->createFolder('TEST');
    }

    public function testUpdateAfterCreateFolderWithType()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $listener = $this->createMock(Horde_Kolab_Storage_List_Manipulation_Listener::class);
        $listener->expects($this->once())
            ->method('updateAfterCreateFolder')
            ->with('TEST', 'event');
        $manipulation->registerListener($listener);
        $manipulation->createFolder('TEST', 'event');
    }

    public function testUpdateAfterDeleteFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $listener = $this->createMock(Horde_Kolab_Storage_List_Manipulation_Listener::class);
        $listener->expects($this->once())
            ->method('updateAfterDeleteFolder')
            ->with('TEST');
        $manipulation->registerListener($listener);
        $manipulation->deleteFolder('TEST');
    }

    public function testUpdateAfterRenameFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Base($driver);
        $listener = $this->createMock(Horde_Kolab_Storage_List_Manipulation_Listener::class);
        $listener->expects($this->once())
            ->method('updateAfterRenameFolder')
            ->with('FOO', 'BAR');
        $manipulation->registerListener($listener);
        $manipulation->renameFolder('FOO', 'BAR');
    }
}
