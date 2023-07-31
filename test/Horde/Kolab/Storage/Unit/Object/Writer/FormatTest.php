<?php
/**
 * Tests the conversion of Kolab MIME parts content to data arrays.
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
 * Tests the conversion of Kolab MIME parts content to data arrays.
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
class Horde_Kolab_Storage_Unit_Object_Writer_FormatTest
extends Horde_Test_Case
{
    public function testLoad()
    {
        $array = array('x' => 'y');
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->getMockBuilder('Horde_Kolab_Format')->getMock();
        $format->expects($this->once())
            ->method('load')
            ->with($content)
            ->will($this->returnValue($array));
        $factory = $this->getMockBuilder('Horde_Kolab_Format_Factory')->getMock();
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
        $object->expects($this->once())
            ->method('setData')
            ->with($array);
        $object->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('event'));
        $this->assertTrue($raw->load($content, $object));
    }

    public function testLoadFailure()
    {
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->getMockBuilder('Horde_Kolab_Format')->getMock();
        $format->expects($this->once())
            ->method('load')
            ->with($content)
            ->will($this->throwException(new Horde_Kolab_Format_Exception()));
        $factory = $this->getMockBuilder('Horde_Kolab_Format_Factory')->getMock();
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
        $object->expects($this->once())
            ->method('setContent')
            ->with($content);
        $object->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('event'));
        $result = $raw->load($content, $object);
        $this->assertInstanceOf('Exception', $result);
    }

    public function testSave()
    {
        $array = array('x' => 'y');
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->getMockBuilder('Horde_Kolab_Format')->getMock();
        $format->expects($this->once())
            ->method('save')
            ->with($array, array('previous' => 'previous'))
            ->will($this->returnValue($content));
        $factory = $this->getMockBuilder('Horde_Kolab_Format_Factory')->getMock();
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
        $object->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($array));
        $object->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('event'));
        $object->expects($this->once())
            ->method('getCurrentContent')
            ->will($this->returnValue('previous'));
        $this->assertSame(
            $content,
            $raw->save($object)
        );
    }

    public function testSaveFailure()
    {
        $this->expectException('Horde_Kolab_Storage_Object_Exception');

        $array = array('x' => 'y');
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->getMockBuilder('Horde_Kolab_Format')->getMock();
        $format->expects($this->once())
            ->method('save')
            ->with($array, array('previous' => 'previous'))
            ->will($this->throwException(new Horde_Kolab_Format_Exception()));
        $factory = $this->getMockBuilder('Horde_Kolab_Format_Factory')->getMock();
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
        $object->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($array));
        $object->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('event'));
        $object->expects($this->once())
            ->method('getCurrentContent')
            ->will($this->returnValue('previous'));
        $raw->save($object);
    }
}
