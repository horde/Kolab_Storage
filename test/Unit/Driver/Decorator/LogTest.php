<?php
/**
 * Test the log decorator for the backend drivers.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Driver\Decorator;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_Driver_Decorator_Log;
use Horde_Log_Logger;
/**
 * Test the log decorator for the backend drivers.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
    public function testGetMailboxesLogsEntry()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getNullMock(),
            $this->getMockLogger()
        );
        $driver->listFolders();
        $this->assertLogCount(2);
    }

    public function testGetMailboxesFolderCount()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getTwoFolderMock(),
            $this->getMockLogger()
        );
        $driver->listFolders();
        $this->assertLogContains('Driver "Horde_Kolab_Storage_Driver_Mock": List contained 2 folders.');
    }

    public function testListAnnotationLogsEntry()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getNullMock(),
            $this->getMockLogger()
        );
        $driver->listAnnotation('/shared/vendor/kolab/folder-type');
        $this->assertLogCount(2);
    }

    public function testListAnnotationFolderCount()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getAnnotatedMock(),
            $this->getMockLogger()
        );
        $driver->listAnnotation('/shared/vendor/kolab/folder-type');
        $this->assertLogContains('Driver "Horde_Kolab_Storage_Driver_Mock": List contained 4 folder annotations.');
    }

    public function testGetNamespaceLogsEntry()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getNullMock(),
            $this->getMockLogger()
        );
        $driver->getNamespace();
        $this->assertLogCount(2);
    }

    public function testGetNamespaceType()
    {
        $driver = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $this->getNullMock(),
            $this->getMockLogger()
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Namespace',
            $driver->getNamespace()
        );
    }

    public function testCreateFolderLog()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Driver "' . get_class($driver) . '": Creating folder INBOX/Test.',
                    'Driver "' . get_class($driver) . '": Successfully created folder INBOX/Test.'
                )
            );
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->create('INBOX/Test');
    }

    public function testDeleteFolderLog()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Driver "' . get_class($driver) . '": Deleting folder INBOX/Test.',
                    'Driver "' . get_class($driver) . '": Successfully deleted folder INBOX/Test.'
                )
            );
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->delete('INBOX/Test');
    }

    public function testRenameFolderLog()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->logicalOr(
                    'Driver "' . get_class($driver) . '": Renaming folder INBOX/Foo.',
                    'Driver "' . get_class($driver) . '": Successfully renamed folder INBOX/Foo to INBOX/Bar.'
                )
            );
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->rename('INBOX/Foo', 'INBOX/Bar');
    }

    public function testCreateFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('create')
            ->with('INBOX/Test');
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->create('INBOX/Test');
    }

    public function testDeleteFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('delete')
            ->with('INBOX/Test');
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->delete('INBOX/Test');
    }

    public function testRenameFolder()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('rename')
            ->with('INBOX/Test', 'FOO');
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(array('debug'))->getMock();
        $log = new Horde_Kolab_Storage_Driver_Decorator_Log($driver, $logger);
        $log->rename('INBOX/Test', 'FOO');
    }

    public function testSetAclLogsEntry()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('setAcl')
            ->with('a', 'b', 'c');
        $logger = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $driver,
            $this->getMockLogger()
        );
        $logger->setAcl('a', 'b', 'c');
        $this->assertLogCount(2);
    }

    public function testDeleteAclLogsEntry()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('deleteAcl')
            ->with('a', 'b');
        $logger = new Horde_Kolab_Storage_Driver_Decorator_Log(
            $driver,
            $this->getMockLogger()
        );
        $logger->deleteAcl('a', 'b');
        $this->assertLogCount(2);
    }
}
