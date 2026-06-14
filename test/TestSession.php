<?php
/**
 * In-memory stub session for unit tests.
 *
 * Satisfies just the Horde_Session subset the Kolab_Storage synchronization
 * code calls (get / set / exists). Avoids the legacy shim's full bootstrap
 * dependency on a Horde_Injector and SessionLifecycle.
 *
 * Usage in tests:
 *   $GLOBALS['session'] = new Horde_Kolab_Storage_Test_Session();
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Test_Session
{
    /** @var array<string, array<string, mixed>> */
    private $data = [];

    public function get($app, $name, $mask = 0)
    {
        return $this->data[$app][$name] ?? null;
    }

    public function set($app, $name, $value, $mask = 0)
    {
        $this->data[$app][$name] = $value;
    }

    public function exists($app, $name)
    {
        return isset($this->data[$app][$name]);
    }
}
