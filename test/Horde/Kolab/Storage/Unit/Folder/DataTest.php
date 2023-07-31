<?php
/**
 * Test the folder data helper.
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
 * Test the folder data helper.
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
class Horde_Kolab_Storage_Unit_Folder_DataTest
extends Horde_Test_Case
{
    public function testType()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('test', $data['type']);
    }

    public function testDefault()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals(true, $data['default']);
    }

    public function testOwner()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('owner', $data['owner']);
    }

    public function testName()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('Test', $data['name']);
    }

    public function testSubpath()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('INBOX/Test', $data['subpath']);
    }

    public function testParent()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('INBOX', $data['parent']);
    }

    public function testNamespace()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('private', $data['namespace']);
    }

    public function testPrefix()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('', $data['prefix']);
    }

    public function testDelimiter()
    {
        $data = $this->_getData()->toArray();
        $this->assertEquals('/', $data['delimiter']);
    }

    private function _getData()
    {
        $type = $this->getMockBuilder('Horde_Kolab_Storage_Folder_Type')->setConstructorArgs(array('a'))->getMock();
        $type->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('test'));
        $type->expects($this->once())
            ->method('isDefault')
            ->will($this->returnValue(true));
        $ns = $this->getMockBuilder('Horde_Kolab_Storage_Folder_Namespace_Element')->setConstructorArgs(array('A', 'B', 'C'))->getMock();
        $namespace = $this->getMockBuilder('Horde_Kolab_Storage_Folder_Namespace')->setConstructorArgs(array(array()))->getMock();
        $namespace->expects($this->once())
            ->method('getOwner')
            ->with('INBOX/Test')
            ->will($this->returnValue('owner'));
        $namespace->expects($this->once())
            ->method('getTitle')
            ->with('INBOX/Test')
            ->will($this->returnValue('Test'));
        $namespace->expects($this->once())
            ->method('getSubpath')
            ->with('INBOX/Test')
            ->will($this->returnValue('INBOX/Test'));
        $namespace->expects($this->once())
            ->method('getParent')
            ->with('INBOX/Test')
            ->will($this->returnValue('INBOX'));
        $namespace->expects($this->exactly(3))
            ->method('matchNamespace')
            ->with('INBOX/Test')
            ->will($this->returnValue($ns));
        $ns->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(''));
        $ns->expects($this->once())
            ->method('getDelimiter')
            ->will($this->returnValue('/'));
        $ns->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('private'));
        return new Horde_Kolab_Storage_Folder_Data(
            'INBOX/Test', $type, $namespace
        );
    }
}
