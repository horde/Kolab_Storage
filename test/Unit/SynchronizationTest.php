<?php
/**
 * Test the synchronization handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Synchronization;
use Horde_Kolab_Storage_List_Synchronization;
use Horde_Kolab_Storage_List_Tools;
use Horde_Kolab_Storage_Data_Base;

/**
 * Test the synchronization handler.
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
class SynchronizationTest
extends TestCase
{
    public function testSynchronizeListReturn()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $list = $this->getMockBuilder(
            Horde_Kolab_Storage_List_Tools::class)->disableOriginalConstructor()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->createMock(Horde_Kolab_Storage_List_Synchronization::class)));
        $this->assertNull($synchronization->synchronizeList($list));
    }

    public function testListSynchronization()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $list = $this->getMockBuilder(
            Horde_Kolab_Storage_List_Tools::class
        )->disableOriginalConstructor()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->createMock(Horde_Kolab_Storage_List_Synchronization::class)));
        $synchronization->synchronizeList($list);
    }

    public function testListSynchronizationInSession()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $list = $this->getMockBuilder(
            'Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->getMock(
        );
        $list->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('test'));
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->createMock(Horde_Kolab_Storage_List_Synchronization::class)));
        $synchronization->synchronizeList($list);
        $this->assertTrue($_SESSION['kolab_storage']['synchronization']['list']['test']);
    }

    public function testDuplicateListSynchronization()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $list = $this->getMockBuilder(
            'Horde_Kolab_Storage_List_Tools')->disableOriginalConstructor()->getMock();
        $list->expects($this->once())
            ->method('getListSynchronization')
            ->will($this->returnValue($this->createMock(Horde_Kolab_Storage_List_Synchronization::class)));
        $synchronization->synchronizeList($list);
        $synchronization->synchronizeList($list);
    }

    public function testSynchronizeDataReturn()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $data = $this->getMockBuilder(
            Horde_Kolab_Storage_Data_Base::class)->disableOriginalConstructor()->getMock();
        $this->assertNull($synchronization->synchronizeData($data));
    }

    public function testDataSynchronization()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $data = $this->getMockBuilder(
            Horde_Kolab_Storage_Data_Base::class)->disableOriginalConstructor()->getMock();
        $data->expects($this->once())
            ->method('synchronize');
        $synchronization->synchronizeData($data);
    }

    public function testDataSynchronizationInSession()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $data = $this->getMockBuilder(
            Horde_Kolab_Storage_Data_Base::class, array(), array(), '', false, false
        )->disableOriginalConstructor()->getMock();
        $data->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('test'));
        $synchronization->synchronizeData($data);
        $this->assertTrue($_SESSION['kolab_storage']['synchronization']['data']['test']);
    }

    public function testDuplicateDataSynchronization()
    {
        $synchronization = new Horde_Kolab_Storage_Synchronization();
        $data = $this->getMockBuilder(
            Horde_Kolab_Storage_Data_Base::class, array(), array(), '', false, false
        )->disableOriginalConstructor()->getMock();
        $data->expects($this->once())
            ->method('synchronize');
        $synchronization->synchronizeData($data);
        $synchronization->synchronizeData($data);
    }

}
