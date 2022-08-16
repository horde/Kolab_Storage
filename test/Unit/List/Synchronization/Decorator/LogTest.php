<?php
/**
 * Tests the synchronisation log decorator.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Synchronization\Decorator;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_List_Synchronization;
use Horde_Kolab_Storage_List_Synchronization_Decorator_Log;
use Horde_Kolab_Storage_List_Synchronization_Listener;
use Horde_Log_Logger;

/**
 * Tests the synchronisation log decorator.
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
    public function testRegisterListener()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Synchronization::class);
        $base->expects($this->once())
            ->method('registerListener');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $listener = $this->createMock(Horde_Kolab_Storage_List_Synchronization_Listener::class);
        $synchronization->registerListener($listener);
    }

    public function testSynchronize()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Synchronization::class);
        $base->expects($this->once())
            ->method('synchronize');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $this->createMock(Horde_Log_Logger::class)
        );
        $synchronization->synchronize();
    }

    public function testSynchronizationLog()
    {
        $base = $this->createMock(Horde_Kolab_Storage_List_Synchronization::class);
        $logger = $this->getMockBuilder(Horde_Log_Logger::class)->addMethods(['debug'])->getMock();
        $logger->expects($this->once())
            ->method('debug')
            ->with('Synchronized the Kolab folder list!');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $logger
        );
        $synchronization->synchronize();
    }
}
