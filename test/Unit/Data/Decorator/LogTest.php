<?php
/**
 * Test the data log decorator.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Unit\Data\Decorator;
use Horde\Kolab\Storage\Test\TestCase;
use Horde_Kolab_Storage_Data;
use Horde_Kolab_Storage_Data_Decorator_Log;

/**
 * Test the data log decorator.
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
class LogTest
extends TestCase
{
    public function testDelete()
    {
        $list = new Horde_Kolab_Storage_Data_Decorator_Log(
            $this->createMock(Horde_Kolab_Storage_Data::class),
            $this->getMockLogger()
        );
        $list->delete('x');
        $this->assertLogCount(2);
    }
}
