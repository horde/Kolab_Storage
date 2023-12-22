<?php
/**
 * Test the synchronization decorator for the storage handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Decorator;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Decorator_Synchronization;
use Horde_Kolab_Storage_Synchronization;
use Horde_Kolab_Storage_List_Tools;
/**
 * Test the synchronization decorator for the storage handler.
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
class SynchronizationTest extends TestCase
{
    public function testList()
    {
        $storage = new Horde_Kolab_Storage_Decorator_Synchronization(
            $this->createStorage($this->getNullMock()),
            new Horde_Kolab_Storage_Synchronization()
        );
        $this->assertInstanceOf(
            Horde_Kolab_Storage_List_Tools::class,
            $storage->getList()
        );
    }
}
