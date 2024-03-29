<?php
/**
 * Test the handling of ACL.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Query\Acl;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_Exception;
use Horde_Kolab_Storage_List_Query_Acl_Base;
/**
 * Test the handling of ACL.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class BaseTest
extends TestCase
{
    public function testHasAclSupport()
    {
        $acl = $this->_getAcl();
        $this->assertTrue($acl->hasAclSupport());
    }

    public function testGetAcl()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('INBOX')
            ->will($this->returnValue('a'));
        $this->driver->expects($this->once())
            ->method('getAcl')
            ->with('INBOX')
            ->will($this->returnValue(array('user' => 'lra')));
        $this->assertEquals(array('user' => 'lra'), $acl->getAcl('INBOX'));
    }

    public function testGetAclWithException()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('INBOX')
            ->will($this->returnValue('a'));
        $this->driver->expects($this->once())
            ->method('getAcl')
            ->with('INBOX')
            ->will($this->throwException(new Horde_Kolab_Storage_Exception()));
        $this->driver->expects($this->once())
            ->method('getAuth')
            ->will($this->returnValue('user'));
        $this->assertEquals(array('user' => 'a'), $acl->getAcl('INBOX'));
    }

    public function testGetAclWithNoAcl()
    {
        $acl = $this->_getAcl(false);
        $this->driver->expects($this->once())
            ->method('getAuth')
            ->will($this->returnValue('current'));
        $this->assertEquals(
            array('current' => 'lrid'), $acl->getAcl('INBOX')
        );
    }

    public function testGetAclWithoutAdminRights()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getAuth')
            ->will($this->returnValue('current'));
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('INBOX')
            ->will($this->returnValue('lr'));
        $this->assertEquals(array('current' => 'lr'), $acl->getAcl('INBOX'));
    }

    public function testGetAclForeignFolderNoAdmin()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('user/example/Notes')
            ->will($this->returnValue('lr'));
        $this->driver->expects($this->once())
            ->method('getAuth')
            ->will($this->returnValue('current'));
        $this->assertEquals(array('current' => 'lr'), $acl->getAcl('user/example/Notes'));
    }

    public function testGetAclForeignFolderWithAdmin()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('user/example/Notes')
            ->will($this->returnValue('lra'));
        $this->driver->expects($this->once())
            ->method('getAcl')
            ->with('user/example/Notes')
            ->will($this->returnValue(array('current' => 'lra')));
        $this->assertEquals(array('current' => 'lra'), $acl->getAcl('user/example/Notes'));
    }

    public function testGetMyAcl()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getMyAcl')
            ->with('INBOX')
            ->will($this->returnValue('lra'));
        $this->assertEquals('lra', $acl->getMyAcl('INBOX'));
    }

    public function testGetMyAclWithNoAcl()
    {
        $acl = $this->_getAcl(false);
        $this->assertEquals('lrid', $acl->getMyAcl('INBOX'));
    }

    public function testGetAllAcl()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('getAcl')
            ->with('INBOX')
            ->will($this->returnValue(array('test' => 'lra')));
        $this->assertEquals(array('test' => 'lra'), $acl->getAllAcl('INBOX'));
    }

    public function testGetAllAclWithNoAcl()
    {
        $acl = $this->_getAcl(false);
        $this->driver->expects($this->once())
            ->method('getAuth')
            ->will($this->returnValue('current'));
        $this->assertEquals(array('current' => 'lrid'), $acl->getAllAcl('INBOX'));
    }

    public function testSetAcl()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('setAcl')
            ->with('INBOX', 'user', 'lra');
        $acl->setAcl('INBOX', 'user', 'lra');
    }

    public function testSetAclWithNoAclSupport()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $acl = $this->_getAcl(false);
        $acl->setAcl('INBOX', 'user', 'lra');
    }

    public function testDeleteAcl()
    {
        $acl = $this->_getAcl();
        $this->driver->expects($this->once())
            ->method('deleteAcl')
            ->with('INBOX', 'user');
        $acl->deleteAcl('INBOX', 'user');
    }

    public function testDeleteAclWithNoAclSupport()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $acl = $this->_getAcl(false);
        $acl->deleteAcl('INBOX', 'user');
    }

    private function _getAcl($has_support = true)
    {
        $this->driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $this->driver->expects($this->any())
            ->method('hasAclSupport')
            ->will($this->returnValue($has_support));
        return new Horde_Kolab_Storage_List_Query_Acl_Base(
            $this->driver
        );
    }
}
