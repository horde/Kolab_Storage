<?php
/**
 * Test the "time based" synchronization handler.
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
 * Test the "time based" synchronization handler.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
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
class Horde_Kolab_Storage_Unit_Synchronization_TimeBasedTest
extends Horde_Test_Case
{
    public function testSynchronizeListReturn()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $list = $this->getMockBuilder('Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock()));
        $this->assertNull($synchronization->synchronizeList($list));
    }

    public function testListSynchronization()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $list = $this->getMockBuilder('Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock()));
        $synchronization->synchronizeList($list);
    }

    public function testListSynchronizationInSession()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $list = $this->getMockBuilder('Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $list->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('test'));
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock()));
        $synchronization->synchronizeList($list);
        $this->assertTrue($_SESSION['kolab_storage']['synchronization']['list']['test']);
    }

    public function testDuplicateListSynchronization()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $list = $this->getMockBuilder('Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->getMockBuilder('Horde_Kolab_Storage_List_Synchronization')->getMock()));
        $synchronization->synchronizeList($list);
        $synchronization->synchronizeList($list);
    }

    public function testSynchronizeDataReturn()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $data = $this->getMockBuilder('Horde_Kolab_Storage_Data_Base')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $this->assertNull($synchronization->synchronizeData($data));
    }

    public function testDataSynchronization()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $data = $this->getMockBuilder('Horde_Kolab_Storage_Data_Base')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $data->expects($this->once())
            ->method('synchronize');
        $synchronization->synchronizeData($data);
    }

    public function testDataSynchronizationInSession()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $data = $this->getMockBuilder('Horde_Kolab_Storage_Data_Base')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $data->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('test'));
        $synchronization->synchronizeData($data);
        $this->assertTrue(isset($_SESSION['kolab_storage']['synchronization']['data']['test']));
    }

    public function testDuplicateDataSynchronization()
    {
        $_SESSION=array();
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased();
        $data = $this->getMockBuilder('Horde_Kolab_Storage_Data_Base')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $data->expects($this->once())
            ->method('synchronize');
        $synchronization->synchronizeData($data);
        $synchronization->synchronizeData($data);
    }

    public function testDuplicateDataSynchronizationAfterSyncTimeElapsed()
    {
        $_SESSION=array();
        $this->markTestSkipped('Test with embedded "sleep()". Only useful for the development phase.');
        $synchronization = new Horde_Kolab_Storage_Synchronization_TimeBased(1, 0);
        $data = $this->getMockBuilder('Horde_Kolab_Storage_Data_Base')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $data->expects($this->exactly(2))
            ->method('synchronize');
        $synchronization->synchronizeData($data);
        sleep(2);
        $synchronization->synchronizeData($data);
    }

}
