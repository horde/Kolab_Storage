<?php
/**
 * Test the factory.
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
use Horde_Log_Logger;
use Horde_Kolab_Storage;
use Horde_Cache;
use Horde_Cache_Storage_Mock;
use Horde_History_Mock;
/**
 * Test the factory.
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
 * @license    http://www.horde.org/l
 * icenses/lgpl21 LGPL 2.1
 */
class FactoryTest
extends TestCase
{
    public function testCreationFromParams()
    {
        $factory = new Horde_Kolab_Storage_Factory(array('driver' => 'mock', 'logger' => $this->createMock(Horde_Log_Logger::class)));
        $this->assertInstanceOf(
            Horde_Kolab_Storage::class,
            $factory->create()
        );
    }

    public function testMissingDriver()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $factory = new Horde_Kolab_Storage_Factory(
            array()
        );
        $factory->createDriver();
    }

    public function testInvalidDriver()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $factory = new Horde_Kolab_Storage_Factory(
            array('driver' => 'something')
        );
        $factory->createDriver();
    }

    public function testMockDriver()
    {
        $factory = new Horde_Kolab_Storage_Factory(
            array('driver' => 'mock')
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Driver_Mock',
            $factory->createDriver()
        );
    }

    public function testInvalidNamespace()
    {
        $this->expectException(Horde_Kolab_Storage_Exception::class);
        $factory = new Horde_Kolab_Storage_Factory();
        $factory->createNamespace(
            'undefined', 'test'
        );
    }

    public function testFixedNamespace()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Namespace_Fixed',
            $factory->createNamespace(
                'fixed', 'test'
            )
        );
    }

    public function testTimerDecoration()
    {
        $logger = $this->getMockLogger();
        $factory = new Horde_Kolab_Storage_Factory(
            array(
                'driver' => 'mock',
                'logger' => $logger,
                'log' => array('driver_time'),
            )
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Driver_Decorator_Timer',
            $factory->createDriver()
        );
    }

    public function testCacheInstance()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $cache = new Horde_Cache(new Horde_Cache_Storage_Mock());
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Cache', $factory->createCache($cache)
        );
    }

    public function testCacheFilebased()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Cache', $factory->createCache(array())
        );
    }

    public function testHistory()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $this->assertInstanceOf(
            'Horde_History', $factory->createHistory('test')
        );
    }

    public function testHistoryInject()
    {
        $history = new Horde_History_Mock('test');
        $factory = new Horde_Kolab_Storage_Factory(
            array('history' => $history)
        );
        $this->assertSame(
            $history, $factory->createHistory('test')
        );
    }

}
