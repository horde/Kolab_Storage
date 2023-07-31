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

/**
 * Tests the rewriting of Kolab MIME part content to a plain content string.
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
class Horde_Kolab_Storage_Unit_Object_Writer_RawTest
extends Horde_Test_Case
{
    public function testLoad()
    {
        $data = "<?xml version=\"1.0\"?>\n<kolab><test/></kolab>";
        $content = fopen('php://temp', 'r+');
        fwrite($content, $data);
        $raw = new Horde_Kolab_Storage_Object_Writer_Raw();
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
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
        $object = $this->getMockBuilder('Horde_Kolab_Storage_Object')->getMock();
        $object->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));
        $this->assertSame(
            $content,
            $raw->save($object)
        );
    }
}
