<?php
/**
 * Test the log decorator for the storage handler.
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
use Horde_Kolab_Storage_Decorator_Log;
use Horde_Kolab_Storage_List_Tools;
/**
 * Test the log decorator for the storage handler.
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
class LogTest extends TestCase
{
    public function testDecoratedList()
    {
        $storage = new Horde_Kolab_Storage_Decorator_Log(
            $this->createStorage($this->getNullMock()),
            $this->getMockLogger()
        );
        $this->assertInstanceOf(
            Horde_Kolab_Storage_List_Tools::class,
            $storage->getList()
        );
    }
}
