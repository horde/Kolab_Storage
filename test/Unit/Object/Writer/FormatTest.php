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
namespace Horde\Kolab\Storage\Test\Unit\Object\Writer;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Format_Exception;
use Horde_Kolab_Format;
use Horde_Kolab_Format_Factory;
use Horde_Kolab_Storage_Object;
use Horde_Kolab_Storage_Object_Exception;
use Horde_Kolab_Storage_Object_Writer_Format;
use Horde_Mime_Part;
use Horde_Mime_Driver;
use Horde_Mime_Headers;

/**
 * Tests the conversion of Kolab MIME parts content to data arrays.
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
class FormatTest
extends TestCase
{
    public function testLoad()
    {
        $array = array('x' => 'y');
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->createMock(Horde_Kolab_Format::class);
        $format->expects($this->once())
            ->method('load')
            ->with($content)
            ->will($this->returnValue($array));
        $factory = $this->createMock(Horde_Kolab_Format_Factory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
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
        $format = $this->createMock(Horde_Kolab_Format::class);
        $format->expects($this->once())
            ->method('load')
            ->with($content)
            ->will($this->throwException(new Horde_Kolab_Format_Exception()));
        $factory = $this->createMock(Horde_Kolab_Format_Factory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
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
        $format = $this->createMock(Horde_Kolab_Format::class);
        $format->expects($this->once())
            ->method('save')
            ->with($array, array('previous' => 'previous'))
            ->will($this->returnValue($content));
        $factory = $this->createMock(Horde_Kolab_Format_Factory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
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

    /**
     * @expectedException Horde_Kolab_Storage_Object_Exception
     */
    public function testSaveFailure()
    {
        $array = array('x' => 'y');
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $format = $this->createMock(Horde_Kolab_Format::class);
        $format->expects($this->once())
            ->method('save')
            ->with($array, array('previous' => 'previous'))
            ->will($this->throwException(new Horde_Kolab_Format_Exception()));
        $factory = $this->createMock(Horde_Kolab_Format_Factory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with('Xml', 'event', array())
            ->will($this->returnValue($format));
        $raw = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $this->expectException(Horde_Kolab_Storage_Object_Exception::class);
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
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
