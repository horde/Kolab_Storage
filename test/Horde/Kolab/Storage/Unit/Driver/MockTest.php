<?php
/**
 * Test the Kolab mock driver.
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
 * Test the Kolab mock driver.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
class Horde_Kolab_Storage_Unit_Driver_MockTest
extends Horde_Kolab_Storage_TestCase
{
    public function testGetMailboxesReturnsArray()
    {
        $this->assertIsArray($this->getNullMock()->listFolders());
    }

    public function testGetMailboxesEmpty()
    {
        $this->assertEquals(array(), $this->getEmptyMock()->listFolders());
    }

    public function testGetMailboxesReturnsMailboxes()
    {
        $this->assertEquals(
            array('INBOX', 'INBOX/a'),
            $this->getTwoFolderMock()->listFolders()
        );
    }

    public function testPersonalFolder()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $this->assertEquals(
            'lrswipkxtecda',
            $mock->getMyAcl('INBOX/Test')
        );
    }

    public function testGetAclFailsOnMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $this->getNullMock()->getAcl('INBOX/test');
    }

    public function testGetAclOnHidden()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', $mock->getAuth(), '');
        $mock->getAcl('INBOX/Test');
    }

    public function testGetAclOnNoAdmin()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', $mock->getAuth(), 'lr');
        try {
            $mock->getAcl('INBOX/Test');
        } catch (Horde_Kolab_Storage_Exception $e) {
            $this->assertEquals('Permission denied!', $e->getMessage());
        }
    }

    public function testGetAclWithAnyone()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anyone', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals(array('anyone' => 'a'), $mock->getAcl('INBOX/Test'));
    }

    public function testGetAclWithAnonymous()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anonymous', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals(array('anonymous' => 'a'), $mock->getAcl('INBOX/Test'));
    }

    public function testGetAclWithGroup()
    {
        $mock = $this->getNullMock();
        $mock->setGroups(array($mock->getAuth() => array('group')));
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'group:group', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals(array('group:group' => 'a'), $mock->getAcl('INBOX/Test'));
    }

    public function testGetMyAclFailsOnMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $this->getNullMock()->getMyAcl('INBOX/test');
    }

    public function testGetMyAclOnHidden()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->getMyAcl('INBOX/Test');
    }

    public function testGetMyAclWithAnyone()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anyone', 'l');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals('l', $mock->getMyAcl('INBOX/Test'));
    }

    public function testGetMyAclWithAnonymous()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anonymous', 'l');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals('l', $mock->getMyAcl('INBOX/Test'));
    }

    public function testGetMyAclWithGroup()
    {
        $mock = $this->getNullMock();
        $mock->setGroups(array($mock->getAuth() => array('group')));
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'group:group', 'l');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $this->assertEquals('l', $mock->getMyAcl('INBOX/Test'));
    }

    public function testSetAclFailsOnMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $this->getNullMock()->setAcl('INBOX/test', 'a', 'b');
    }

    public function testSetAclOnHidden()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->setAcl('INBOX/Test', 'a', 'b');
    }

    public function testSetAclOnNoAdmin()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', $mock->getAuth(), 'lr');
        try {
            $mock->setAcl('INBOX/Test', 'a', 'b');
        } catch (Horde_Kolab_Storage_Exception $e) {
            $this->assertEquals('Permission denied!', $e->getMessage());
        }
    }

    public function testSetAclWithAnyone()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anyone', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->setAcl('INBOX/Test', 'a', 'b');
    }

    public function testSetAclWithAnonymous()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anonymous', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->setAcl('INBOX/Test', 'a', 'b');
    }

    public function testSetAclWithGroup()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->setGroups(array($mock->getAuth() => array('group')));
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'group:group', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->setAcl('INBOX/Test', 'a', 'b');
    }

    public function testDeleteAclFailsOnMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $this->getNullMock()->deleteAcl('INBOX/test', 'a');
    }

    public function testDeleteAclOnHidden()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', $mock->getAuth(), '');
        $mock->deleteAcl('INBOX/Test', 'a');
    }

    public function testDeleteAclOnNoAdmin()
    {
        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', $mock->getAuth(), 'lr');
        try {
            $mock->deleteAcl('INBOX/Test', 'a');
        } catch (Horde_Kolab_Storage_Exception $e) {
            $this->assertEquals('Permission denied!', $e->getMessage());
        }
    }

    public function testDeleteAclWithAnyone()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anyone', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->deleteAcl('INBOX/Test', 'anyone');
    }

    public function testDeleteAclWithAnonymous()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'anonymous', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->deleteAcl('INBOX/Test', 'anonymous');
    }

    public function testDeleteAclWithGroup()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getNullMock();
        $mock->setGroups(array($mock->getAuth() => array('group')));
        $mock->create('INBOX/Test');
        $mock->setAcl('INBOX/Test', 'group:group', 'a');
        $mock->deleteAcl('INBOX/Test', $mock->getAuth());
        $mock->deleteAcl('INBOX/Test', 'group:group');
    }

    public function testSetAnnotationFailsOnMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $this->getNullMock()->setAnnotation('INBOX/test', 'a', 'b');
    }

    public function testListAnnotationReturnsArray()
    {
        $this->assertIsArray(
            $this->getNullMock()->listAnnotation(
                '/shared/vendor/kolab/folder-type'
            )
        );
    }

    public function testListAnnotationSize()
    {
        $this->assertEquals(
            4,
            count(
                $this->getAnnotatedMock()->listAnnotation(
                    '/shared/vendor/kolab/folder-type'
                )
            )
        );
    }

    public function testListAnnotationKeys()
    {
        $this->assertEquals(
            array('INBOX/Calendar', 'INBOX/Contacts', 'INBOX/Notes', 'INBOX/Tasks'),
            array_keys(
                $this->getAnnotatedMock()->listAnnotation(
                    '/shared/vendor/kolab/folder-type'
                )
            )
        );
    }

    public function testListAnnotationGermanKeys()
    {
        $this->assertEquals(
            array('INBOX/Kalender', 'INBOX/Kontakte', 'INBOX/Notizen', 'INBOX/Aufgaben'),
            array_keys(
                $this->getGermanAnnotatedMock()->listAnnotation(
                    '/shared/vendor/kolab/folder-type'
                )
            )
        );
    }

    public function testGetAnnotationReturnsAnnotationValue()
    {
        $data = array(
            'username' => 'test',
            'data' => array(
                'user/test/Contacts' => array(
                    'annotations' => array(
                        '/shared/vendor/kolab/folder-type' => 'contact.default',
                    ),
                    'permissions' => array('anyone' => 'lrid'),
                ),
            ),
        );
        $driver = new Horde_Kolab_Storage_Driver_Mock(
            new Horde_Kolab_Storage_Factory(), $data
        );
        $this->assertEquals(
            'contact.default',
            $driver->getAnnotation('INBOX/Contacts', '/shared/vendor/kolab/folder-type')
        );
    }

    public function testNullAuth()
    {
        $data = array(
            'username' => '',
            'data' => array(
                'user/test/Contacts' => array(
                    'permissions' => array('test' => 'lrid'),
                ),
                'user/a' => array(
                    'permissions' => array('anyone' => 'lrid'),
                ),
                'shared.Something' => array(
                    'permissions' => array('anyone' => 'lrid'),
                ),
            ),
        );
        $driver = new Horde_Kolab_Storage_Driver_Mock(
            new Horde_Kolab_Storage_Factory(), $data
        );
        $this->assertEquals(
            array('user/test/Contacts', 'user/a', 'shared.Something'),
            $driver->listFolders()
        );
    }

    public function testGetNamespaceReturnsNamespaceHandler()
    {
        $driver = new Horde_Kolab_Storage_Driver_Mock(
            new Horde_Kolab_Storage_Factory(), array()
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Namespace',
            $driver->getNamespace()
        );
    }

    public function testGetNamespaceReturnsExpectedNamespaces()
    {
        $driver = new Horde_Kolab_Storage_Driver_Mock(
            new Horde_Kolab_Storage_Factory(), array('username' => 'test')
        );
        $namespaces = array();
        foreach ($driver->getNamespace() as $namespace) {
            $namespaces[$namespace->getName()] = array(
                'type' => $namespace->getType(),
                'delimiter' => $namespace->getDelimiter(),
            );
        }
        $this->assertEquals(
            array(
                'INBOX' => array(
                    'type' => 'personal',
                    'delimiter' => '/',
                ),
                'user' => array(
                    'type' => 'other',
                    'delimiter' => '/',
                ),
                '' => array(
                    'type' => 'shared',
                    'delimiter' => '/',
                ),
            ),
            $namespaces
        );
    }

    public function testGetIdReturnsString()
    {
        $this->assertIsString($this->getNullMock()->getId());
    }

    public function testSelect()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getMessageMock();
        $mock->select('INBOX/Test');
    }

    public function testSelected()
    {
        $mock = $this->getMessageMock();
        $mock->select('INBOX/Test');
        $status = $mock->status('INBOX/Test');
        $this->assertEquals(1, $status['uidnext']);
    }

    public function testMissing()
    {
        $this->expectException('Horde_Kolab_Storage_Exception');

        $mock = $this->getNullMock();
        $mock->select('INBOX/Test');
    }

    public function testSelectUmlaut()
    {
        $mock = $this->getMessageMock();
        $mock->select('INBOX/ÄÖÜ');
        $status = $mock->status('INBOX/ÄÖÜ');
        $this->assertEquals(1, $status['uidnext']);
    }

    public function testGetUids()
    {
        $this->expectNotToPerformAssertions();

        $mock = $this->getMessageMock();
        $mock->getUids('INBOX/Test');
    }

    public function testGetUidList()
    {
        $mock = $this->getMessageMock();
        $this->assertEquals(
            array(),
            $mock->getUids('INBOX/Test')
        );
    }

    public function testGetUidListSelected()
    {
        $mock = $this->getMessageMock();
        $this->assertEquals(
            array(1),
            $mock->getUids('INBOX/Pretend')
        );
    }

    public function testGetUidWithoutDeleted()
    {
        $mock = $this->getMessageMock();
        $this->assertEquals(
            array(4),
            $mock->getUids('INBOX/WithDeleted')
        );
    }

    public function testGetStamp()
    {
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Stamp',
            $this->getMessageMock()->getStamp('INBOX/WithDeleted')
        );
    }

    public function testStructureArray()
    {
        $structure = $this->getMessageMock()
            ->fetchStructure(
                'INBOX/Calendar', array(1)
            );
        $this->assertInstanceOf(
            'Horde_Mime_Part',
            $structure[1]['structure']
        );
    }

    public function testBodypartResource()
    {
        $this->assertIsResource(
            $this->getMessageMock()
            ->fetchBodypart(
                'INBOX/Calendar', 4, '2'
            )
        );
    }
}
