<?php
/**
 * Tests the folder type factory.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Folder;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Folder_Types;

/**
 * Tests the folder type factory.
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
class TypesTest
extends TestCase
{
    public function testConstruction()
    {
        $this->assertInstanceOf(Horde_Kolab_Storage_Folder_Types::class, new Horde_Kolab_Storage_Folder_Types());
    }

    public function testType()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Type',
            $types->create('event')
        );
    }

    public function testTypeContact()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertEquals('contact', $types->create('contact')->getType());
    }

    public function testTypeDefaultEvent()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertEquals('event', $types->create('event.default')->getType());
    }

    public function testTypeDefaultIsDefault()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertTrue($types->create('contact.default')->isDefault());
    }

    public function testNoDefault()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertFalse($types->create('contact')->isDefault());
    }

    public function testSame()
    {
        $types = new Horde_Kolab_Storage_Folder_Types();
        $this->assertSame(
            $types->create('contact'), $types->create('contact')
        );
    }
}
