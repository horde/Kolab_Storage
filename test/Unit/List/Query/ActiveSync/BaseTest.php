<?php
/**
 * Test the handling of active sync data.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Query\ActiveSync;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_List_Query_ActiveSync_Base;

/**
 * Test the handling of active sync data.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
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
    public function testGetEmptyActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/priv/vendor/kolab/activesync')
            ->will($this->returnValue(''));
        $this->assertEquals(null, $activesync->getActiveSync('INBOX'));
    }

    public function testGetValidActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/priv/vendor/kolab/activesync')
            ->will($this->returnValue('eyJ4IjoieSJ9'));
        $this->assertEquals(array('x' => 'y'), $activesync->getActiveSync('INBOX'));
    }

    public function testGetInvalidBase64ActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/priv/vendor/kolab/activesync')
            ->will($this->returnValue('#&&'));
        $this->assertEquals(null, $activesync->getActiveSync('INBOX'));
    }

    public function testGetInvalidJsonActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/priv/vendor/kolab/activesync')
            ->will($this->returnValue('SGFsbG8K'));
        $this->assertEquals(null, $activesync->getActiveSync('INBOX'));
    }

    public function testSetActiveSync()
    {
        $activesync = $this->_getActivesync();
        $this->driver->expects($this->once())
            ->method('setAnnotation')
            ->with('INBOX', '/priv/vendor/kolab/activesync', 'eyJ4IjoieSJ9');
        $activesync->setActiveSync('INBOX', array('x' => 'y'));
    }

    private function _getActivesync()
    {
        $this->driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        return new Horde_Kolab_Storage_List_Query_ActiveSync_Base(
            $this->driver
        );
    }
}
