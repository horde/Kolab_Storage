<?php
/**
 * A folder stamp that includes a list of UIDs.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * A folder stamp that includes a list of UIDs.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Folder_Stamp_Uids
implements Horde_Kolab_Storage_Folder_Stamp
{
    /** The UID validity status */
    const UIDVALIDITY = 'uidvalidity';

    /** The next UID status */
    const UIDNEXT = 'uidnext';

    /** The sync token  */
    const TOKEN = 'token';

    /**
     * The folder status.
     *
     * @var array
     */
    protected $_status;

    /**
     * The list of backend object IDs.
     *
     * @var array
     */
    protected $_ids;

    /**
     * Constructor.
     *
     * @param array $status The folder status.
     * @param array $ids    The list of undeleted objects in the folder.
     */
    public function __construct($status, $ids)
    {
        $this->_status = $status;
        $this->_ids    = $ids;
    }

    /**
     * Return the folder UID validity.
     *
     * @return string The folder UID validity marker.
     */
    public function uidvalidity()
    {
        return $this->_status[self::UIDVALIDITY];
    }

    /**
     * Return the folder next UID number.
     *
     * @return string The next UID number.
     */
    public function uidnext()
    {
        return $this->_status[self::UIDNEXT];
    }

    /**
     * Return the backend object IDs in the folder.
     *
     * @return array The list of backend IDs.
     */
    public function ids()
    {
        return $this->_ids;
    }

    /**
     * Return the sync token.
     *
     * @return string|boolen The token provided by the IMAP client, or false
     *                       if unavailable.
     */
    public function getToken()
    {
        return !empty($this->_status[self::TOKEN])
            ? $this->_status[self::TOKEN]
            : false;
    }

    /**
     * Indicate if there was a complete folder reset.
     *
     * @param Horde_Kolab_Storage_Folder_Stamp_Uids The stamp to compare against.
     *
     * @return boolean True if there was a complete folder reset stamps are
     *                 different, false if not.
     */
    public function isReset(Horde_Kolab_Storage_Folder_Stamp $stamp)
    {
        if (!$stamp instanceof Horde_Kolab_Storage_Folder_Stamp_Uids) {
            throw new Horde_Kolab_Storage_Exception('This stamp can only be compared against stamps of its own type.');
        }
        if ($this->uidvalidity() != $stamp->uidvalidity()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * What changed between this old stamp and the new provided stamp?
     *
     * @param Horde_Kolab_Storage_Folder_Stamp_Uids $stamp  The new stamp to
     *                                                      compare against.
     *
     * @return array  An array of two elements (added IDs, deleted IDs).
     */
    public function getChanges(Horde_Kolab_Storage_Folder_Stamp $stamp)
    {
        if (!$stamp instanceof Horde_Kolab_Storage_Folder_Stamp_Uids) {
            throw new Horde_Kolab_Storage_Exception('This stamp can only be compared against stamps of its own type.');
        }
        return array(
            self::DELETED => array_values(
                array_diff($this->ids(), $stamp->ids())
            ),
            self::ADDED => array_values(
                array_diff($stamp->ids(), $this->ids())
            )
        );
    }

    /**
     * Serialize this object.
     *
     * @return string  The serialized data.
     */
    public function serialize()
    {
        return serialize(array($this->_status, $this->_ids));
    }
    public function __serialize(): array
    {
        return [$this->_status, $this->_ids];
    }

    /**
     * Reconstruct the object from serialized data.
     *
     * @param string $data  The serialized data.
     */
    public function unserialize($data)
    {
        list($this->_status, $this->_ids) = @unserialize($data);
    }
    public function __unserialize(array $data): void
    {
        list($this->_status, $this->_ids) = $data;
    }

    /**
     * Convert the instance into a string.
     *
     * @return string The string representation for this instance.
     */
    public function __toString()
    {
        return sprintf(
            "uidvalidity: %s\nuidnext: %s\nuids: %s\ntoken: %s",
            $this->uidvalidity(),
            $this->uidnext(),
            join(', ', $this->ids()),
            !empty($this->_status[self::TOKEN]) ? $this->_status[self::TOKEN] : '--'
        );
    }
}
