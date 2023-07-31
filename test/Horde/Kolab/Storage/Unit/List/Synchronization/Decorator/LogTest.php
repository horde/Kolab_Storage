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

/**
 * Tests the synchronisation log decorator.
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Unit_List_Synchronization_Decorator_LogTest
extends Horde_Test_Case
{
    public function testRegisterListener()
    {
        $base = $this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock();
        $base->expects($this->once())
            ->method('registerListener');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $this->getMockBuilder('Horde_Log_Logger')->getMock()
        );
        $listener = $this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization_Listener')->getMock();
        $synchronization->registerListener($listener);
    }

    public function testSynchronize()
    {
        $base = $this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock();
        $base->expects($this->once())
            ->method('synchronize');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $this->getMockBuilder('Horde_Log_Logger')->getMock()
        );
        $synchronization->synchronize();
    }

    public function testSynchronizationLog()
    {
        $base = $this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock();
        $logger = $this->getMockBuilder('Horde_Log_Logger')->setMethods(array('debug'))->getMock();
        $logger->expects($this->once())
            ->method('debug')
            ->with('Synchronized the Kolab folder list!');
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Decorator_Log(
            $base, $logger
        );
        $synchronization->synchronize();
    }
}
