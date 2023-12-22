<?php
/**
 * Test the stamp based on UIDs.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Folder\Stamp;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Folder_Stamp;
use Horde_Kolab_Storage_Folder_Stamp_Uids;
use Horde_Kolab_Storage_Exception;

/**
 * Test the stamp based on UIDs.
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
class UidsTest
extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->status = array('uidvalidity' => '99', 'uidnext' => '5', 'token' => 'somestamp');
        $this->uids = array(1, 2, 4);
    }

    public function testUidValidity()
    {
        $this->assertEquals('99', $this->_getStamp()->uidvalidity());
    }

    public function testUidNext()
    {
        $this->assertEquals('5', $this->_getStamp()->uidnext());
    }

    public function testIds()
    {
        $this->assertEquals(array(1, 2, 4), $this->_getStamp()->ids());
    }

    public function testNoReset()
    {
        $this->assertFalse($this->_getStamp()->isReset($this->_getStamp()));
    }

    public function testReset()
    {
        $stamp = new Horde_Kolab_Storage_Folder_Stamp_Uids(
            array('uidvalidity' => '100', 'uidnext' => '5'),
            $this->uids
        );
        $this->assertTrue($this->_getStamp()->isReset($stamp));
    }

    public function testInvalidStampTypeForReset()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getStamp()->isReset($this->createMock(Horde_Kolab_Storage_Folder_Stamp::class));
    }

    public function testSerialize()
    {
        $this->assertEquals(
            serialize(array($this->status, $this->uids)),
            $this->_getStamp()->serialize()
        );
    }

    public function testSerialize2()
    {
        // previously tested on identic encoding - which is irrelevant as long as decoding is the same
        $this->assertEquals(
            unserialize('C:37:"Horde_Kolab_Storage_Folder_Stamp_Uids":128:{a:2:{i:0;a:3:{s:11:"uidvalidity";s:2:"99";s:7:"uidnext";s:1:"5";s:5:"token";s:9:"somestamp";}i:1;a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}}'),
            $this->_getStamp()
        );
    }

    public function testUnserialize()
    {
        $this->assertEquals(
            array(
                Horde_Kolab_Storage_Folder_Stamp::DELETED => array(),
                Horde_Kolab_Storage_Folder_Stamp::ADDED => array(),
            ),
            $this->_getStamp()->getChanges(
                unserialize(serialize($this->_getStamp()))
            )
        );
    }

    public function testNoChange()
    {
        $this->assertEquals(
            array(
                Horde_Kolab_Storage_Folder_Stamp::DELETED => array(),
                Horde_Kolab_Storage_Folder_Stamp::ADDED => array(),
            ),
            $this->_getStamp()->getChanges($this->_getStamp())
        );
    }

    public function testAdded()
    {
        $stamp = new Horde_Kolab_Storage_Folder_Stamp_Uids(
            array('uidvalidity' => '99', 'uidnext' => '6'),
            array(1, 2, 4, 6)
        );
        $this->assertEquals(
            array(
                Horde_Kolab_Storage_Folder_Stamp::DELETED => array(),
                Horde_Kolab_Storage_Folder_Stamp::ADDED => array(6),
            ),
            $this->_getStamp()->getChanges($stamp)
        );
    }

    public function testDeleted()
    {
        $stamp = new Horde_Kolab_Storage_Folder_Stamp_Uids(
            $this->status,
            array(1, 4)
        );
        $this->assertEquals(
            array(
                Horde_Kolab_Storage_Folder_Stamp::DELETED => array(2),
                Horde_Kolab_Storage_Folder_Stamp::ADDED => array(),
            ),
            $this->_getStamp()->getChanges($stamp)
        );
    }

    public function testUpdated()
    {
        $stamp = new Horde_Kolab_Storage_Folder_Stamp_Uids(
            array('uidvalidity' => '99', 'uidnext' => '8'),
            array(4, 6, 7)
        );
        $this->assertEquals(
            array(
                Horde_Kolab_Storage_Folder_Stamp::DELETED => array(1, 2),
                Horde_Kolab_Storage_Folder_Stamp::ADDED => array(6, 7),
            ),
            $this->_getStamp()->getChanges($stamp)
        );
    }

    public function testInvalidStampType()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->_getStamp()->getChanges($this->createMock(Horde_Kolab_Storage_Folder_Stamp::class));
    }

    public function testToString()
    {
        $this->assertEquals(
            "uidvalidity: 99\nuidnext: 5\nuids: 1, 2, 4\ntoken: somestamp",
            (string) $this->_getStamp()
        );
    }

    private function _getStamp()
    {
        return new Horde_Kolab_Storage_Folder_Stamp_Uids($this->status, $this->uids);
    }
}
