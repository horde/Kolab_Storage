<?php
/**
 * Test the handling of ACL.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Storage
 */

/**
 * Prepare the test setup.
 */
require_once 'Autoload.php';

/**
 * Test the handling of ACL.
 *
 * Copyright 2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Storage
 */
class Horde_Kolab_Storage_AclTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_storage = $this->getMock('Horde_Kolab_Storage', array(), array(), '', false, false);
        $this->_imap = $this->getMock('Horde_Imap_Client_Socket', array(), array(), '', false, false);
        $this->groups = $this->getMock('Group', array(), array(), '', false, false);
        $this->_connection = new Horde_Kolab_Storage_Driver_Imap($this->_imap, $this->groups);
        $this->_imap->expects($this->any())
            ->method('getNamespaces')
            ->will(
                $this->returnValue(
                    array(
                        array(
                            'name'      => 'INBOX/',
                            'type'      =>  Horde_Kolab_Storage_Namespace::PERSONAL,
                            'delimiter' => '/',
                        ),
                        array(
                            'name'      => 'user/',
                            'type'      =>  Horde_Kolab_Storage_Namespace::OTHER,
                            'delimiter' => '/',
                        ),
                        array(
                            'name'      => '',
                            'type'      =>  Horde_Kolab_Storage_Namespace::SHARED,
                            'delimiter' => '/',
                        )
                    )
                )
            );
    }

    public function testGetaclRetrievesFolderAcl()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('listMailboxes')
            ->will($this->returnValue(array('INBOX')));
        $this->_imap->expects($this->once())
            ->method('getAcl')
            ->with('INBOX')
            ->will($this->returnValue(array('user' => array('l', 'r', 'a'))));
        $folder = $this->_getFolder('INBOX');
        $this->assertEquals(array('user' => 'lra'), $folder->getAcl());
    }

    public function testGetaclRetrievesMyFolderAclOnFolderWithNoAdminRights()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('listMailboxes')
            ->will($this->returnValue(array('INBOX')));
        $this->_imap->expects($this->once())
            ->method('getAcl')
            ->with('INBOX')
            ->will($this->throwException(new Horde_Imap_Client_Exception()));
        $this->_imap->expects($this->once())
            ->method('getMyACLRights')
            ->with('INBOX')
            ->will($this->returnValue('lr'));
        $this->_imap->expects($this->any())
            ->method('getParam')
            ->with('username')
            ->will($this->returnValue('user'));
        $folder = $this->_getFolder('INBOX');
        $this->assertEquals(array('user' => 'lr'), $folder->getAcl());
    }

    public function testGetaclRetrievesMyFolderAclOnForeignFolderWithNoAdminRights()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('listMailboxes')
            ->will($this->returnValue(array('INBOX')));
        $this->_imap->expects($this->once())
            ->method('getMyACLRights')
            ->with('user/test')
            ->will($this->returnValue('lr'));
        $this->_imap->expects($this->any())
            ->method('getParam')
            ->with('username')
            ->will($this->returnValue('test'));
        $folder = $this->_getFolder('user/test');
        $this->assertEquals(array('test' => 'lr'), $folder->getAcl());
    }

    public function testGetaclRetrievesAllFolderAclOnForeignFolderWithAdminRights()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('listMailboxes')
            ->will($this->returnValue(array('INBOX')));
        $this->_imap->expects($this->once())
            ->method('getMyACLRights')
            ->with('user/test')
            ->will($this->returnValue('lra'));
        $this->_imap->expects($this->once())
            ->method('getAcl')
            ->with('user/test')
            ->will($this->returnValue(array('test' => 'lra')));
        $this->_imap->expects($this->any())
            ->method('getParam')
            ->with('username')
            ->will($this->returnValue('test'));
        $folder = $this->_getFolder('user/test');
        $this->assertEquals(array('test' => 'lra'), $folder->getAcl());
    }

    public function testSetacletsFolderAcl()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('setAcl')
            ->with('INBOX', 'user', array('rights' => 'lra'));
        $folder = $this->_getFolder('INBOX');
        $folder->setAcl('user', 'lra');
    }

    public function testDeleteaclDeletesFolderAcl()
    {
        $this->_supportAcl();
        $this->_imap->expects($this->once())
            ->method('setAcl')
            ->with('INBOX', 'user', array('remove' => true));
        $folder = $this->_getFolder('INBOX');
        $folder->deleteAcl('user');
    }

    public function testGetaclRetrievesDefaultAclIfAclAreNotSupported()
    {
        $this->_imap->expects($this->once())
            ->method('queryCapability')
            ->with('ACL')
            ->will($this->returnValue(false));
        $this->_imap->expects($this->once())
            ->method('listMailboxes')
            ->will($this->returnValue(array('INBOX')));
        $this->_imap->expects($this->any())
            ->method('getParam')
            ->with('username')
            ->will($this->returnValue('user'));
        $folder = $this->_getFolder('INBOX');
        $this->assertEquals(array('user' => 'lrid'), $folder->getAcl());
    }

    public function testSetaclDoesNothingIfAclAreNotSupported()
    {
        $this->_imap->expects($this->once())
            ->method('queryCapability')
            ->with('ACL')
            ->will($this->returnValue(false));
        $folder = $this->_getFolder('INBOX');
        $folder->setAcl('user', 'lr');
    }

    public function testDeleteaclDoesNothingIfAclAreNotSupported()
    {
        $this->_imap->expects($this->once())
            ->method('queryCapability')
            ->with('ACL')
            ->will($this->returnValue(false));
        $folder = $this->_getFolder('INBOX');
        $folder->deleteAcl('user', 'lr');
    }

    private function _getFolder($name)
    {
        $folder = new Horde_Kolab_Storage_Folder_Base($name);
        $folder->restore($this->_storage, $this->_connection);
        return $folder;
    }

    private function _supportAcl()
    {
        $this->_imap->expects($this->any())
            ->method('queryCapability')
            ->with($this->logicalOr('ACL', 'NAMESPACE'))
            ->will($this->returnValue(true));
    }
}