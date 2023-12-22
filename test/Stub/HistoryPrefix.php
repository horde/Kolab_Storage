<?php
/**
 * History system prefix generator for use with Kolab_Storage.
 *
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Kolab\Storage\Test\Stub;
use Horde_Kolab_Storage_HistoryPrefix;
use Horde_Kolab_Storage_Data;

/**
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class HistoryPrefix
implements Horde_Kolab_Storage_HistoryPrefix
{
    /**
     * Mock mapping. Basically the stock Horde app mapping.
     *
     * @var array
     */
    protected static $_map = array(
        'contact' => 'turba',
        'event' => 'kronolith',
        'note' => 'mnemo',
        'task' => 'nag'
    );

    /**
     * Generate a prefix for the History system for the given Kolab data.
     *
     * @param  Horde_Kolab_Storage_Data $data  The data object.
     *
     * @return string  The History prefix.
     */
    public static function getPrefix(Horde_Kolab_Storage_Data $data)
    {
        $app = empty(self::$_map[$data->getType()])
            ? false
            : self::$_map[$data->getType()];

        return empty($app)
            ? false
            : sprintf('%s:internal_id:', $app);
    }

}