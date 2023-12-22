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
namespace Horde\Kolab\Storage\Test\Unit\Driver;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Driver_Pear;
use Horde_Kolab_Storage_Factory;
use Net_IMAP;
/**
 * Test the Kolab mock driver.
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
class PearTest
extends TestCase
{
    public function testGetNamespaceReturnsNamespaceHandler()
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        $driver = new Horde_Kolab_Storage_Driver_Pear(
            new Horde_Kolab_Storage_Factory(),
            array('backend' => $this->_getNamespaceMock())
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Folder_Namespace',
            $driver->getNamespace()
        );
    }

    public function testGetNamespaceReturnsExpectedNamespaces()
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        $driver = new Horde_Kolab_Storage_Driver_Pear(
            new Horde_Kolab_Storage_Factory(),
            array('backend' => $this->_getNamespaceMock())
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

    private function _getNamespaceMock()
    {
        $imap = $this->getMockBuilder(Net_IMAP::class)->onlyMethods( array('hasCapability', 'getNameSpace'))->getMock();
        $imap->expects($this->once())
            ->method('hasCapability')
            ->with('NAMESPACE')
            ->will($this->returnValue(true));
        $imap->expects($this->once())
            ->method('getNamespace')
            ->will(
                $this->returnValue(
                    array(
                        'personal' => array(
                            array(
                                'name' => 'INBOX',
                                'delimter' => '/',
                            )
                        ),
                        'others' => array(
                            array(
                                'name' => 'user',
                                'delimter' => '/',
                            )
                        ),
                        'shared' => array(
                            array(
                                'name' => '',
                                'delimter' => '/',
                            )
                        ),
                    )
                )
            );
        return $imap;
    }
}
