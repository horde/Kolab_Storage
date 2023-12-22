<?php
/**
 * Tests the modification of existing Kolab mime messages.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\ComponentTest\Data\Object\Message;
use PHPUnit\Framework\TestCase;
use Horde\Kolab\Storage\Test\Stub\Driver;
use Horde_Kolab_Format_Factory;
use Horde_Kolab_Storage_Object;
use Horde_Kolab_Storage_Object_Writer_Format;

/**
 * Tests the modification of existing Kolab mime messages.
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
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
class ModifiedTest
extends TestCase
{
    public function testStore()
    {
        $driver = new Driver('user');
        $driver->setMessage('INBOX', 1, file_get_contents(__DIR__ . '/../../../../fixtures/note.eml'));
        $factory = new Horde_Kolab_Format_Factory();
        $writer = new Horde_Kolab_Storage_Object_Writer_Format(
            $factory
        );
        $object = new Horde_Kolab_Storage_Object();
        $object->setDriver($driver);
        $folder = $this->createMock(Horde_Kolab_Storage_Folder::class);
        $folder->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('INBOX'));
        $folder->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('note'));
        $structure = $driver->fetchComplete('INBOX', 1);
        $object->load(1, $folder, $writer, $structure[1]);
        $object->setData(
            array('summary' => 'NEW', 'description' => 'test', 'uid' => 'ABC1234')
        );
        $object->save($writer);
        $result = $driver->messages['INBOX'][2];
        $result = preg_replace(
            array(
                '/=20/',
                '/Date: .*/',
                '/boundary=".*"/',
                '/--=_.*/',
                '/<creation-date>[^<]*/',
                '/<last-modification-date>[^<]*/',
                '/\r\n/',
                '/=\n/',
            ),
            array(
                ' ',
                'Date: ',
                'boundary=""',
                '--=_',
                '<creation-date>',
                '<last-modification-date>',
                "\n",
                '',
            ),
            $result
        );
        $this->assertStringMatchesFormat(
            'From: user
To: user
Date: 
Subject: ABC1234
User-Agent: Horde::Kolab::Storage v%s
MIME-Version: 1.0
X-Kolab-Type: application/x-vnd.kolab.note
Content-Type: multipart/mixed; boundary="";
 name="Kolab Groupware Data"
Content-Disposition: attachment; filename="Kolab Groupware Data"

This message is in MIME format.

--=_
Content-Type: text/plain; charset=utf-8; name="Kolab Groupware Information"
Content-Disposition: inline; filename="Kolab Groupware Information"

This is a Kolab Groupware object. To view this object you will need an email
client that understands the Kolab Groupware format. For a list of such email
clients please visit http://www.kolab.org/content/kolab-clients
--=_
Content-Type: application/x-vnd.kolab.note; name=kolab.xml
Content-Disposition: inline; x-kolab-type=xml; filename=kolab.xml

<?xml version="1.0" encoding="UTF-8"?>
<note version="1.0">
  <uid>ABC1234</uid>
  <body/>
  <categories/>
  <creation-date></creation-date>
  <last-modification-date></last-modification-date>
  <sensitivity>public</sensitivity>
  <product-id>Horde_Kolab_Format_Xml-%s (api version: 2)</product-id>
  <summary>NEW</summary>
  <x-test>other client</x-test>
  <background-color>#000000</background-color>
  <foreground-color>#ffff00</foreground-color>
</note>

--=_
',
            $result
        );
    }
}
