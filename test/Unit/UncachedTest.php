<?php
/**
 * Test the basic storage handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Exception;
use Horde_Kolab_Storage_Factory;
use Horde_Kolab_Storage_List_Tools;
use Horde_Kolab_Storage_Uncached;

/**
 * Test the basic storage handler.
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
class UncachedTest
extends TestCase
{
    public function testConstruction()
    {
        $this->assertInstanceOf(
            Horde_Kolab_Storage_Uncached::class, 
            $this->createStorage()
        );
    }

    public function testGetList()
    {
        $this->assertInstanceOf(
            Horde_Kolab_Storage_List_Tools::class,
            $this->createStorage()->getList()
        );
    }

    public function testSameList()
    {
        $base = $this->createStorage();
        $this->assertSame($base->getList(), $base->getList());
    }

    public function testGetFolder()
    {
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder',
            $this->createStorage($this->getAnnotatedMock())->getFolder('INBOX')
        );
    }

    public function testGetData()
    {
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Data',
            $this->createStorage($this->getAnnotatedMock())->getData('INBOX')
        );
    }

    public function testSameData()
    {
        $base = $this->createStorage($this->getAnnotatedMock());
        $this->assertSame(
            $base->getData('INBOX'), $base->getData('INBOX')
        );
    }

    public function testDifferentFolders()
    {
        $base = $this->createStorage($this->getAnnotatedMock());
        $this->assertNotSame(
            $base->getData('INBOX'), $base->getData('INBOX/a')
        );
    }

    public function testGetSystemList()
    {
        $params = array('system' => array('' => array('username' => 'system', 'password' => '')));
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_List_Tools',
            $this->_getSystemStorage($params)->getSystemList('test')
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testNoSystemUser()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_List',
            $this->createStorage()->getSystemList('test')
        );
    }

    private function _getSystemStorage($params)
    {
        $factory = new Horde_Kolab_Storage_Factory(
            array(
                'driver' => 'mock',
                'params' => array(
                    'username' => 'test@example.com',
                    'host' => 'localhost',
                    'port' => 143,
                    'data' => array()
                )
            )
        );
        return $this->createStorage(null, $factory, $params);
    }

}
