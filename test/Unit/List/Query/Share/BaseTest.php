<?php
/**
 * Test the handling of share data.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\List\Query\Share;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_List_Query_Share_Base;

/**
 * Test the handling of share data.
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
    public function testGetDescription()
    {
        $share = $this->_getShare();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/shared/comment')
            ->will($this->returnValue('description'));
        $this->assertEquals('description', $share->getDescription('INBOX'));
    }

    public function testGetParameters()
    {
        $share = $this->_getShare();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/shared/vendor/horde/share-params')
            ->will($this->returnValue(base64_encode(serialize(array('params')))));
        $this->assertEquals(array('params'), $share->getParameters('INBOX'));
    }

    public function testGetEmptyParameters()
    {
        $share = $this->_getShare();
        $this->driver->expects($this->once())
            ->method('getAnnotation')
            ->with('INBOX', '/shared/vendor/horde/share-params')
            ->will($this->returnValue(''));
        $this->assertEquals(array(), $share->getParameters('INBOX'));
    }

    public function testSetDescription()
    {
        $share = $this->_getShare();
        $this->driver->expects($this->once())
            ->method('setAnnotation')
            ->with('INBOX', '/shared/comment', 'test');
        $share->setDescription('INBOX', 'test');
    }

    public function testSetParameters()
    {
        $share = $this->_getShare();
        $this->driver->expects($this->once())
            ->method('setAnnotation')
            ->with(
                'INBOX',
                '/shared/vendor/horde/share-params',
                base64_encode(serialize(array('params')))
            );
        $share->setParameters('INBOX', array('params'));
    }

    private function _getShare()
    {
        $this->driver = $this->createMock(Horde_Kolab_Storage_Driver::class);
        return new Horde_Kolab_Storage_List_Query_Share_Base(
            $this->driver
        );
    }
}
