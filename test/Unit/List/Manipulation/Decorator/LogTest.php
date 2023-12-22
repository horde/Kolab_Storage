<?php
/**
 * Test the operations of the list manipulation log decorator.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Manipulation\Decorator;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_List_Manipulation;
use Horde_Kolab_Storage_List_Manipulation_Decorator_Log;
use Horde_Kolab_Storage_List_Manipulation_Listener;
use Horde_Log_Logger;
/**
 * Test the operations of the list manipulation log decorator.
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
class LogTest
extends TestCase
{
    public function testCreateFolder()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $base->expects($this->once())
            ->method('createFolder')
            ->with('TEST');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $manipulation->createFolder('TEST');
    }

    public function testDeleteFolder()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $base->expects($this->once())
            ->method('deleteFolder')
            ->with('TEST');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $manipulation->deleteFolder('TEST');
    }

    public function testRenameFolder()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $base->expects($this->once())
            ->method('renameFolder')
            ->with('FOO', 'BAR');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $manipulation->renameFolder('FOO', 'BAR');
    }

    public function testRegisterListener()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $base->expects($this->once())
            ->method('registerListener');
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $listener = $this->createMock(Horde_Kolab_Storage_List_Manipulation_Listener::class);
        $manipulation->registerListener($listener);
    }

    public function testCreateFolderLog()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Creating folder TEST.',
                    'Successfully created folder TEST [type: ].'
                )
            );
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $logger
        );
        $manipulation->createFolder('TEST');
    }

    public function testDeleteFolderLog()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Deleting folder TEST.',
                    'Successfully deleted folder TEST.'
                )
            );
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $logger
        );
        $manipulation->deleteFolder('TEST');
    }

    public function testRenameFolderLog()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Manipulation::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Renaming folder FOO.',
                    'Successfully renamed folder FOO to BAR.'
                )
            );
        $manipulation = new Horde_Kolab_Storage_List_Manipulation_Decorator_Log(
            $base, $logger
        );
        $manipulation->renameFolder('FOO', 'BAR');
    }
}
