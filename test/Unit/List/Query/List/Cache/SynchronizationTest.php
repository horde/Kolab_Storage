<?php
/**
 * Test the synchronization machinery.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Query\List\Cache;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_Folder_Namespace;
use Horde_Kolab_Storage_Folder_Namespace_Element;
use Horde_Kolab_Storage_Folder_Namespace_Fixed;
use Horde_Kolab_Storage_Folder_Types;
use Horde_Kolab_Storage_List_Cache;
use Horde_Kolab_Storage_List_Exception;
use Horde_Kolab_Storage_List_Query_List_Base;
use Horde_Kolab_Storage_List_Query_List_Cache;
use Horde_Kolab_Storage_List_Query_List_Cache_Synchronization;
use Horde_Kolab_Storage_List_Query_List_Defaults_Bail;

/**
 * Test the synchronization machinery.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
class SynchronizationTest
extends TestCase
{
    public function testSynchronizeNamespace()
    {
        $synchronization = $this->_getSynchronization();
        $synchronization->setCache($this->cache);
        $this->cache->expects($this->once())
            ->method('setNamespace')
            ->with('N;');
        $synchronization->synchronize($this->cache);
    }

    public function testSynchronizeFolderlist()
    {
        $synchronization = $this->_getSynchronization();
        $synchronization->setCache($this->cache);
        $this->cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Test'),
                array('INBOX/Test' => 'a.default')
            );
        $synchronization->synchronize($this->cache);
    }

    public function testSynchronizeQueries()
    {
        $synchronization = $this->_getSynchronization();
        $synchronization->setCache($this->cache);
        $this->cache->expects($this->exactly(6))
            ->method('setQuery')
            ->with(
                $this->logicalOr(
                    Horde_Kolab_Storage_List_Query_List_Cache::TYPES,
                    Horde_Kolab_Storage_List_Query_List_Cache::FOLDERS,
                    Horde_Kolab_Storage_List_Query_List_Cache::OWNERS,
                    Horde_Kolab_Storage_List_Query_List_Cache::BY_TYPE,
                    Horde_Kolab_Storage_List_Query_List_Cache::DEFAULTS,
                    Horde_Kolab_Storage_List_Query_List_Cache::PERSONAL_DEFAULTS
                ),
                $this->logicalOr(
                    array(
                        'INBOX/Test' => 'a'
                    ),
                    array(
                        'INBOX/Test' => array(
                            'folder' => 'INBOX/Test',
                            'type' => 'a',
                            'default' => true,
                            'owner' => 'owner',
                            'name' => 'Test',
                            'subpath' => 'INBOX/Test',
                            'parent' => 'INBOX',
                            'namespace' => 'personal',
                            'prefix' => '',
                            'delimiter' => '/',
                        )
                    ),
                    array(
                        'INBOX/Test' => 'owner'
                    ),
                    array(
                        'a' => array(
                            'INBOX/Test' => array(
                                'folder' => 'INBOX/Test',
                                'type' => 'a',
                                'default' => true,
                                'owner' => 'owner',
                                'name' => 'Test',
                                'subpath' => 'INBOX/Test',
                                'parent' => 'INBOX',
                                'namespace' => 'personal',
                                'prefix' => '',
                                'delimiter' => '/',
                            )
                        )
                    ),
                    array(
                        'owner' => array('a' => 'INBOX/Test')
                    ),
                    array(
                        'a' => 'INBOX/Test'
                    )
                )
            );
        $synchronization->synchronize($this->cache);
    }

    public function testUpdateAfterCreateFolderExit()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(false));
        $cache->expects($this->never())
            ->method('getFolders');
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterCreateFolder('INBOX/FooBar');
    }

    public function testUpdateAfterCreateFolder()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo', 'INBOX/Bar')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo', 'INBOX/Bar', 'INBOX/FooBar'),
                array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterCreateFolder('INBOX/FooBar');
    }

    public function testUpdateAfterCreateFolderWithType()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo', 'INBOX/Bar')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo', 'INBOX/Bar', 'INBOX/FooBar'),
                array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note', 'INBOX/FooBar' => 'note')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterCreateFolder('INBOX/FooBar', 'note');
    }

    public function testUpdateAfterDeleteFolderExit()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(false));
        $cache->expects($this->never())
            ->method('getFolders');
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterDeleteFolder('INBOX/FooBar');
    }

    public function testUpdateAfterDeleteFolder()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo', 'INBOX/Bar')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo'),
                array('INBOX/Foo' => 'contact')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterDeleteFolder('INBOX/Bar');
    }

    public function testUpdateAfterRenameFolderExit()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(false));
        $cache->expects($this->never())
            ->method('getFolders');
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterRenameFolder('INBOX/Foo', 'INBOX/FooBar');
    }

    public function testUpdateAfterRenameFolder()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo', 'INBOX/Bar')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'contact', 'INBOX/Bar' => 'note')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo', 'INBOX/FooBar'),
                array('INBOX/Foo' => 'contact', 'INBOX/FooBar' => 'note')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->updateAfterRenameFolder('INBOX/Bar', 'INBOX/FooBar');
    }

    private function _getSynchronization()
    {
        $this->driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $this->cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $this->driver->expects($this->once())
            ->method('listFolders')
            ->will($this->returnValue(array('INBOX/Test')));
        $this->driver->expects($this->once())
            ->method('listAnnotation')
            ->with(Horde_Kolab_Storage_List_Query_List_Base::ANNOTATION_FOLDER_TYPE)
            ->will($this->returnValue(array('INBOX/Test' => 'a.default')));
        $ns = $this->getMockBuilder(Horde_Kolab_Storage_Folder_Namespace_Element::class)->disableOriginalConstructor()->getMock();
        $namespace = $this->getMockBuilder(Horde_Kolab_Storage_Folder_Namespace::class)->disableOriginalConstructor()->getMock();
        $namespace->expects($this->exactly(2))
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
            ->will($this->returnValue('personal'));
        $this->driver->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue($namespace));
        return new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $this->driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
    }

    public function testGetDuplicateDefaults()
    {
        $duplicates = array('a' => 'b');
        $defaults = $this->createMock(Horde_Kolab_Storage_List_Query_List_Defaults_Bail::class);
        $defaults->expects($this->once())
            ->method('getDuplicates')
            ->will($this->returnValue($duplicates));
        $synchronization = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $this->createMock(Horde_Kolab_Storage_Driver::class), new Horde_Kolab_Storage_Folder_Types(), $defaults
        );
        $this->assertEquals($duplicates, $synchronization->getDuplicateDefaults());
    }

    public function testSetDefaultExit()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(false));
        $cache->expects($this->never())
            ->method('getFolders');
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->setDefault('INBOX/Foo');
    }

    public function testSetDefault()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->once())
            ->method('setAnnotation')
            ->with(
                'INBOX/Foo',
                Horde_Kolab_Storage_List_Query_List_Base::ANNOTATION_FOLDER_TYPE,
                'contact.default'
            );

        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'contact')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo'),
                array('INBOX/Foo' => 'contact.default')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->setDefault(
            array(
                'folder' => 'INBOX/Foo',
                'namespace' => 'personal',
                'type' => 'contact'
            )
        );
    }

    public function testSetDefaultFailsWithoutPreviousType()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->onlyMethods(['hasNamespace', 'getFolderTypes'])->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/FooBar' => 'contact')));
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $this->expectException(Horde_Kolab_Storage_List_Exception::class);
        $list->setDefault(
            array(
                'folder' => 'INBOX/Foo',
                'namespace' => 'personal',
                'type' => 'contact'
            )
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_List_Exception
     */
    public function testSetDefaultFailsOutsidePersonalNamespace()
    {
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->onlyMethods(['hasNamespace', 'getFolderTypes'])->disableOriginalConstructor()->getMock();
        $cache->expects($this->once())
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $this->expectException(Horde_Kolab_Storage_List_Exception::class);
        $list->setDefault(
            array(
                'folder' => 'INBOX/Foo',
                'namespace' => 'shared',
                'type' => 'contact'
            )
        );
    }

    public function testSetDefaultResetPreviousDefault()
    {
        $namespace = new Horde_Kolab_Storage_Folder_Namespace_Fixed('test');
        $driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        $driver->expects($this->exactly(2))
            ->method('setAnnotation')
            ->with(
                $this->logicalOr(
                    'INBOX/Foo',
                    'INBOX/Bar'
                ),
                Horde_Kolab_Storage_List_Query_List_Base::ANNOTATION_FOLDER_TYPE,
                $this->logicalOr(
                    'event.default',
                    'event'
                )
            );

        $cache = $this->getMockBuilder(Horde_Kolab_Storage_List_Cache::class)->disableOriginalConstructor()->getMock();
        $cache->expects($this->exactly(2))
            ->method('hasNamespace')
            ->will($this->returnValue(true));
        $cache->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue(serialize($namespace)));
        $cache->expects($this->once())
            ->method('getFolders')
            ->will($this->returnValue(array('INBOX/Foo', 'INBOX/Bar')));
        $cache->expects($this->once())
            ->method('getFolderTypes')
            ->will($this->returnValue(array('INBOX/Foo' => 'event', 'INBOX/Bar' => 'event.default')));
        $cache->expects($this->once())
            ->method('store')
            ->with(
                array('INBOX/Foo', 'INBOX/Bar'),
                array('INBOX/Foo' => 'event.default', 'INBOX/Bar' => 'event')
            );
        $list = new Horde_Kolab_Storage_List_Query_List_Cache_Synchronization(
            $driver,
            new Horde_Kolab_Storage_Folder_Types(),
            new Horde_Kolab_Storage_List_Query_List_Defaults_Bail()
        );
        $list->setCache($cache);
        $list->setDefault(
            array(
                'folder' => 'INBOX/Foo',
                'namespace' => 'personal',
                'type' => 'event'
            ),
            'INBOX/Bar'
        );
    }
}
