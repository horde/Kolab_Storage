<?php
/**
 * Test the handling of attachments.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test;
use PHPUnit\Framework\TestCase;
use Horde_Kolab_Storage_Data;
use Horde_Util;
/**
 * Test the handling of attachments.
 *
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
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
class AttachmentTest extends TestCase
{
    /**
     * Test setup.
     *
     */
    public function setUp(): void
    {
        $this->markTestIncomplete('Broken test, prepareBasicSetup() doesn\'t exist');

        $world = $this->prepareBasicSetup();

        $this->storage = $this->authenticate($world['auth'],
                         'wrobel@example.org',
                         'none');

        $this->folder = $this->prepareNewFolder($this->storage, 'Contacts', 'contact', true);
    }

    /**
     * Test destruction.
     */
    public function tearDown(): void
    {
        if ($this->storage) {
            $this->storage->clean();
        }
    }

    /**
     * Test storing attachments.
     *
     * @return NULL
     */
    public function testCacheAttachmentInFile()
    {
        $data = new Horde_Kolab_Storage_Data('contact');
        $data->setFolder($this->folder);

        $atc1 = Horde_Util::getTempFile();
        $fh = fopen($atc1, 'w');
        fwrite($fh, 'test');
        fclose($fh);

        $object = array('uid' => '1',
                        'full-name' => 'User Name',
                        'email' => 'user@example.org',
                        'inline-attachment' => array('test.txt'),
                        '_attachments' => array('test.txt'=> array('type' => 'text/plain',
                                                                   'path' => $atc1,
                                                                   'name' => 'test.txt')));

        $result = $data->save($object);
        $this->assertNoError($result);
        $result = $data->getObject(1);
        $this->assertNoError($result);
        $this->assertTrue(isset($result['_attachments']['test.txt']));
        // @todo: what introduces the \r?
        $this->assertEquals("test\r", $data->getAttachment($result['_attachments']['test.txt']['key']));
    }

    /**
     * Test storing attachments.
     *
     * @return void
     */
    public function testCacheAttachmentAsContent()
    {
        $data = new Horde_Kolab_Storage_Data('contact');
        $data->setFolder($this->folder);

        $object = array('uid' => '1',
                        'full-name' => 'User Name',
                        'email' => 'user@example.org',
                        'inline-attachment' => array('test.txt'),
                        '_attachments' => array('test.txt'=> array('type' => 'text/plain',
                                                                   'content' => 'test',
                                                                   'name' => 'test.txt')));

        $result = $data->save($object);
        $this->assertNoError($result);
        $result = $data->getObject(1);
        $this->assertNoError($result);
        $this->assertTrue(isset($result['_attachments']['test.txt']));
        $this->assertEquals("test\r", $data->getAttachment($result['_attachments']['test.txt']['key']));
    }
}
