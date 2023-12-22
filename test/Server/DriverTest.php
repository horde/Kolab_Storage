<?php
/**
 * Server test of the different driver implementations.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Server;
use PHPUnit\Framework\TestCase;
use Horde_Group_Mock;
use Horde_Imap_Client;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_Driver_Cclient;
use Horde_Kolab_Storage_Driver_Mock;
use Horde_Kolab_Storage_Driver_Imap;
use Horde_Kolab_Storage_Driver_Pear;
/**
 * Server test of the different driver implementations.
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
class DriverTest extends TestCase
{
    const MOCK         = 'Mock';
    const CCLIENT      = 'Cclient';
    const PEAR         = 'Pear';
    const IMAP_SOCKET  = 'Imap_Socket';

    public function setUp(): void
    {
        if (!isset($this->sharedFixture)) {
            $this->markTestSkipped('Testing of a running server skipped. No configuration fixture available.');
            return;
        }

        /** Setup group handler */
        $this->group = new Horde_Group_Mock();


    }

    public function tearDown(): void
    {
        /** Reactivate strict reporting as we need to turn it off for PEAR-Net_IMAP */
        if (!empty($this->old_error_reporting)) {
            error_reporting($this->old_error_reporting);
        }
    }

    public function provideDrivers()
    {
        return array(
            'mock driver' => array(self::MOCK),
            'PHP c-client based driver' => array(self::CCLIENT),
            'PEAR-Net_IMAP based driver' => array(self::PEAR),
            'Horde_Imap_Client_Socket based driver' => array(self::IMAP_SOCKET),
        );
    }

    private function _getDriver($driver)
    {
        if ($driver == self::PEAR) {
            /** PEAR-Net_IMAP is not E_STRICT */
            $this->old_error_reporting = error_reporting(E_ALL & ~E_STRICT);
        }
        if (!isset($this->sharedFixture->drivers[$driver])) {
            switch ($driver) {
            case self::MOCK:
                $connection = new Horde_Kolab_Storage_Driver_Mock($this->group);
                break;
            case self::CCLIENT:
                $connection = new Horde_Kolab_Storage_Driver_Cclient(
                    $this->group
                );
                break;
            case self::PEAR:
                $client = new Net_IMAP($this->sharedFixture->conf['host'], 143, false);
                $client->login(
                    $this->sharedFixture->conf['user'],
                    $this->sharedFixture->conf['pass']
                );

                $connection = new Horde_Kolab_Storage_Driver_Pear(
                    $client,
                    $this->group
                );
                break;
            case self::IMAP_SOCKET:
                $params = array(
                    'hostspec' => $this->sharedFixture->conf['host'],
                    'username' => $this->sharedFixture->conf['user'],
                    'password' => $this->sharedFixture->conf['pass'],
                    'debug'    => $this->sharedFixture->conf['debug'],
                    'port'     => 143,
                    'secure'   => false
                );
                $client = Horde_Imap_Client::factory('socket', $params);
                $client->login();

                $connection = new Horde_Kolab_Storage_Driver_Imap(
                    $client,
                    $this->group
                );
                break;
            default:
                exit("Undefined storage driver!\n");
            }
            $this->sharedFixture->drivers[$driver] = $connection;
        }
        return $this->sharedFixture->drivers[$driver];
    }

    /**
     * @dataProvider provideDrivers
     */
    public function testDriverType($driver)
    {
        $this->assertInstanceOf(Horde_Kolab_Storage_Driver::class, $this->_getDriver($driver));
    }

    /**
     * @dataProvider provideDrivers
     */
    public function testGetNamespace($driver)
    {
        $namespaces = array();
        foreach ($this->_getDriver($driver)->getNamespace() as $namespace) {
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
}
