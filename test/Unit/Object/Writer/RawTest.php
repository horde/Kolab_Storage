<?php
/**
 * Tests the rewriting of Kolab MIME part content to a plain content string.
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
use Horde_Kolab_Storage_Object;
use Horde_Kolab_Storage_Object_Writer_Raw;
use Horde_Mime_Part;
use Horde_Mime_Driver;
use Horde_Mime_Headers;

/**
 * Tests the rewriting of Kolab MIME part content to a plain content string.
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
class RawTest
extends TestCase
{
    public function testLoad()
    {
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $raw = new Horde_Kolab_Storage_Object_Writer_Raw();
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
        $object->expects($this->once())
            ->method('setContent')
            ->with($content);
        $raw->load($content, $object);
    }

    public function testSave()
    {
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $raw = new Horde_Kolab_Storage_Object_Writer_Raw();
        $object = $this->createMock(Horde_Kolab_Storage_Object::class);
        $object->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));
        $this->assertSame(
            $content,
            $raw->save($object)
        );
    }
}
