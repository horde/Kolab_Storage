<?php
/**
 * Synchronization strategy that synchronizes once per session with the backend.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Synchronization strategy that synchronizes once per session with the backend.
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
class Horde_Kolab_Storage_Synchronization_OncePerSession
extends Horde_Kolab_Storage_Synchronization
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Synchronize the provided list in case the selected synchronization
     * strategy requires it.
     *
     * @param Horde_Kolab_Storage_List $list The list to synchronize.
     */
    public function synchronizeList(Horde_Kolab_Storage_List_Tools $list)
    {
        $session = $GLOBALS['session'];
        $list_id = $list->getId();
        $key = 'synchronization/list/' . $list_id;
        if (empty($session->get('kolab_storage', $key))) {
            $list->getListSynchronization()->synchronize();
            $session->set('kolab_storage', $key, true);
        }
    }

    /**
     * Synchronize the provided data in case the selected synchronization
     * strategy requires it.
     *
     * @param Horde_Kolab_Storage_Data $data The data to synchronize.
     */
    public function synchronizeData(Horde_Kolab_Storage_Data $data)
    {
        $session = $GLOBALS['session'];
        $data_id = $data->getId();
        $key = 'synchronization/data/' . $data_id;
        if (empty($session->get('kolab_storage', $key))) {
            $data->synchronize();
            $session->set('kolab_storage', $key, true);
        }
    }
}