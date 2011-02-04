<?php
/**
 * An cclient based Kolab storage driver.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Storage
 */

/**
 * An cclient based Kolab storage driver.
 *
 * Copyright 2010-2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Storage
 */
class Horde_Kolab_Storage_Driver_Cclient
extends Horde_Kolab_Storage_Driver_Base
{
    /**
     * Server name.
     *
     * @var string
     */
    private $_host;

    /**
     * Basic IMAP connection string.
     *
     * @var string
     */
    private $_base_mbox;

    /**
     * The currently selected mailbox.
     *
     * @var string
     */
    private $_selected;

    /**
     * Create the backend driver.
     *
     * @return mixed The backend driver.
     */
    public function createBackend()
    {
        if (!function_exists('imap_open')) {
            throw new Horde_Kolab_Storage_Exception('The IMAP extension is not available!');
        }
        $result = @imap_open(
            $this->_getBaseMbox(),
            $this->getParam('username'),
            $this->getParam('password'),
            OP_HALFOPEN
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Connecting to server %s failed. Error: %s"
                    ),
                    $this->_getHost(),
                    imap_last_error()
                )
            );
        }
        return $result;
    }

    /**
     * Return the root mailbox of the current user.
     *
     * @return string The id of the user that opened the IMAP connection.
     */
    private function _getBaseMbox()
    {
        if (!isset($this->_base_mbox)) {
            $this->_base_mbox = '{' . $this->_getHost()
                . ':' . $this->getParam('port') . '/imap';
            $secure = $this->getParam('secure');
            if (!empty($secure)) {
                $this->_base_mbox .= '/' . $secure . '/novalidate-cert';
            } else {
                $this->_base_mbox .= '/notls';
            }
            $this->_base_mbox .= '}';
        }
        return $this->_base_mbox;
    }

    /**
     * Return the root mailbox of the current user.
     *
     * @return string The id of the user that opened the IMAP connection.
     */
    private function _getHost()
    {
        if (!isset($this->_host)) {
            $this->_host = $this->getParam('host');
            if (empty($this->_host)) {
                throw new Horde_Kolab_Storage_Exception(
                    Horde_Kolab_Storage_Translation::t(
                        "Missing \"host\" parameter!"
                    )
                );
            }
        }
        return $this->_host;
    }

    /**
     * Retrieves a list of mailboxes on the server.
     *
     * @return array The list of mailboxes.
     *
     * @throws Horde_Kolab_Storage_Exception In case listing the folders failed.
     */
    public function getMailboxes()
    {
        return $this->decodeList($this->_getMailboxes());
    }

    /**
     * Retrieves a UTF7-IMAP encoded list of mailboxes on the server.
     *
     * @return array The list of mailboxes.
     *
     * @throws Horde_Kolab_Storage_Exception In case listing the folders failed.
     */
    private function _getMailboxes()
    {
        $folders = array();

        $result = imap_list($this->getBackend(), $this->_getBaseMbox(), '*');
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Listing folders for %s failed. Error: %s"
                    ),
                    $this->_getBaseMbox(),
                    imap_last_error()
                )
            );
        }

        $root = $this->_getBaseMbox();
        $server_len = strlen($root);
        foreach ($result as $folder) {
            if (substr($folder, 0, $server_len) == $root) {
                $folders[] = substr($folder, $server_len);
            }
        }

        return $folders;
    }

    /**
     * Create the specified folder.
     *
     * @param string $folder The folder to create.
     *
     * @return NULL
     */
    public function create($folder)
    {
        $result = imap_createmailbox(
            $this->getBackend(),
            $this->_getBaseMbox() . $this->encodePath($folder)
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Creating folder %s%s failed. Error: %s"
                    ),
                    $this->_getBaseMbox(),
                    $folder,
                    imap_last_error()
                )
            );
        }
    }

    /**
     * Delete the specified folder.
     *
     * @param string $folder  The folder to delete.
     *
     * @return NULL
     */
    public function delete($folder)
    {
        $result = imap_deletemailbox(
            $this->getBackend(),
            $this->_getBaseMbox() . $this->encodePath($folder)
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Deleting folder %s%s failed. Error: %s"
                    ),
                    $this->_getBaseMbox(),
                    $folder,
                    imap_last_error()
                )
            );
        }
    }

    /**
     * Rename the specified folder.
     *
     * @param string $old  The folder to rename.
     * @param string $new  The new name of the folder.
     *
     * @return NULL
     */
    public function rename($old, $new)
    {
        $result = imap_renamemailbox(
            $this->getBackend(),
            $this->_getBaseMbox() . $this->encodePath($old),
            $this->_getBaseMbox() . $this->encodePath($new)
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Renaming folder %s%s to %s%s failed. Error: %s"
                    ),
                    $this->_getBaseMbox(),
                    $old,
                    $this->_getBaseMbox(),
                    $new,
                    imap_last_error()
                )
            );
        }
    }

    /**
     * Does the backend support ACL?
     *
     * @return boolean True if the backend supports ACLs.
     */
    public function hasAclSupport()
    {
        @imap_getacl(
            $this->getBackend(),
            $this->_getBaseMbox()
        );
        if (imap_last_error()  == 'ACL not available on this IMAP server') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Retrieve the access rights for a folder.
     *
     * @param string $folder The folder to retrieve the ACL for.
     *
     * @return array An array of rights.
     */
    public function getAcl($folder)
    {
        $result = imap_getacl($this->getBackend(), $this->encodePath($folder));
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Failed reading ACL on folder %s. Error: %s"
                    ),
                    $folder,
                    imap_last_error()
                )
            );
        }
        return $result;
    }

    /**
     * Retrieve the access rights the current user has on a folder.
     *
     * @param string $folder The folder to retrieve the user ACL for.
     *
     * @return string The user rights.
     */
    public function getMyAcl($folder)
    {
        if (!function_exists('imap_myrights')) {
            throw new Horde_Kolab_Storage_Exception('PHP does not support imap_myrights.');
        }

        $result = imap_myrights($this->getBackend(), $this->encodePath($folder));
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Failed reading user rights on folder %s. Error: %s"
                    ),
                    $folder,
                    imap_last_error()
                )
            );
        }
        return $result;
    }

    /**
     * Set the access rights for a folder.
     *
     * @param string $folder  The folder to act upon.
     * @param string $user    The user to set the ACL for.
     * @param string $acl     The ACL.
     *
     * @return NULL
     */
    public function setAcl($folder, $user, $acl)
    {
        $result = imap_setacl($this->getBackend(), $this->encodePath($folder), $user, $acl);
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Failed setting ACL on folder %s for user %s to %acl. Error: %s"
                    ),
                    $folder,
                    $user,
                    $acl,
                    imap_last_error()
                )
            );
        }
        return $result;
    }

    /**
     * Delete the access rights for user on a folder.
     *
     * @param string $folder  The folder to act upon.
     * @param string $user    The user to delete the ACL for
     *
     * @return NULL
     */
    public function deleteAcl($folder, $user)
    {
        $this->setAcl($folder, $user, '');
    }

    /**
     * Retrieves the specified annotation for the complete list of mailboxes.
     *
     * @param string $annotation The name of the annotation to retrieve.
     *
     * @return array An associative array combining the folder names as key with
     *               the corresponding annotation value.
     */
    public function listAnnotation($annotation)
    {
        if (!function_exists('imap_getannotation')) {
            throw new Horde_Kolab_Storage_Exception(
                'This driver is not supported by your variant of PHP. The function "imap_getannotation" is missing!'
            );
        }
        list($entry, $value) = $this->_getAnnotateMoreEntry($annotation);
        $list = array();
        foreach ($this->_getMailboxes() as $mailbox) {
            $result = imap_getannotation($this->getBackend(), $mailbox, $entry, $value);
            if (isset($result[$value])) {
                $list[$mailbox] = $result[$value];
            }
        }
        return $this->decodeListKeys($list);
    }

    /**
     * Fetches the annotation from a folder.
     *
     * @param string $mailbox    The name of the folder.
     * @param string $annotation The annotation to get.
     *
     * @return string The annotation value.
     */
    public function getAnnotation($mailbox, $annotation)
    {
        list($entry, $key) = $this->_getAnnotateMoreEntry($annotation);
        $result = imap_getannotation(
            $this->getBackend(), $this->encodePath($mailbox), $entry, $key
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Retrieving annotation %s[%s] on folder %s%s failed. Error: %s"
                    ),
                    $entry,
                    $key,
                    $this->_getBaseMbox(),
                    $mailbox,
                    imap_last_error()
                )
            );
        }
        return $result[$key];
    }

    /**
     * Sets the annotation on a folder.
     *
     * @param string $mailbox    The name of the folder.
     * @param string $annotation The annotation to set.
     * @param array  $value      The values to set
     *
     * @return NULL
     */
    public function setAnnotation($mailbox, $annotation, $value)
    {
        list($entry, $key) = $this->_getAnnotateMoreEntry($annotation);
        $result = imap_setannotation(
            $this->getBackend(), $this->encodePath($mailbox), $entry, $key, $value
        );
        if (!$result) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Setting annotation %s[%s] on folder %s%s to %s failed. Error: %s"
                    ),
                    $entry,
                    $key,
                    $this->_getBaseMbox(),
                    $mailbox,
                    $value,
                    imap_last_error()
                )
            );
        }
    }

    /**
     * Opens the given folder.
     *
     * @param string $folder  The folder to open
     *
     * @return NULL
     */
    public function select($folder)
    {
        $selection = $this->_getBaseMbox() . $this->encodePath($folder);
        if ($this->_selected != $selection) {
            $result = imap_reopen($this->getBackend(), $selection);
            if (!$result) {
                throw new Horde_Kolab_Storage_Exception(
                    sprintf(
                        Horde_Kolab_Storage_Translation::t(
                            "Failed opening folder %s%s. Error: %s"
                        ),
                        $this->_getBaseMbox(),
                        $mailbox,
                        imap_last_error()
                    )
                );
            }
        }
    }

    /**
     * Returns the status of the current folder.
     *
     * @param string $folder Check the status of this folder.
     *
     * @return array  An array that contains 'uidvalidity' and 'uidnext'.
     */
    public function status($folder)
    {
        $this->select($folder);
        $status = imap_status_current($this->getBackend(), SA_MESSAGES | SA_UIDVALIDITY | SA_UIDNEXT);
        if (!$status) {
            /**
             * @todo: The cclient method seems pretty much unable to detect
             * missing folders. It always returns "true"
             */
            throw new Horde_Kolab_Storage_Exception(
                sprintf(
                    Horde_Kolab_Storage_Translation::t(
                        "Failed retrieving status information for folder %s. Error: %s"
                    ),
                    $this->_getBaseMbox(),
                    $mailbox,
                    imap_last_error()
                )
            );
        }
        return array(
            'uidvalidity' => $status->uidvalidity,
            'uidnext' => $status->uidnext
        );
    }

    /**
     * Returns the message ids of the messages in this folder.
     *
     * @param string $folder Check the status of this folder.
     *
     * @return array  The message ids.
     */
    public function getUids($folder)
    {
        $this->select($folder);
        $uids = imap_search($this->getBackend(), 'UNDELETED', SE_UID);
        /**
         * @todo Error recognition? Nada... :(
         */
        if (!is_array($uids)) {
            $uids = array();
        }
        return $uids;
    }


    /**
     * Appends a message to the current folder.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     * @param string $msg     The message to append.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function appendMessage($mailbox, $msg)
    {
        return $this->_imap->append($mailbox, array(array('data' => $msg)));
    }

    /**
     * Deletes messages from the current folder.
     *
     * @param integer $uids  IMAP message ids.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function deleteMessages($mailbox, $uids)
    {
        if (!is_array($uids)) {
            $uids = array($uids);
        }
        return $this->_imap->store($mailbox, array('add' => array('\\deleted'), 'ids' => $uids));
    }

    /**
     * Moves a message to a new folder.
     *
     * @param integer $uid        IMAP message id.
     * @param string $new_folder  Target folder.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function moveMessage($old_folder, $uid, $new_folder)
    {
        $options = array('ids' => array($uid), 'move' => true);
        return $this->_imap->copy($old_folder, $new_folder, $options);
    }

    /**
     * Expunges messages in the current folder.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function expunge($mailbox)
    {
        return $this->_imap->expunge($mailbox);
    }

    /**
     * Retrieves the message headers for a given message id.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     * @param int $uid                The message id.
     * @param boolean $peek_for_body  Prefetch the body.
     *
     * @return mixed  The message header or a PEAR error in case of an error.
     */
    public function getMessageHeader($mailbox, $uid, $peek_for_body = true)
    {
        $options = array('ids' => array($uid));
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->headerText();

        $result = $this->_imap->fetch($mailbox, $query, $options);
        return $result[$uid]['headertext'][0];
    }

    /**
     * Retrieves the message body for a given message id.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     * @param integet $uid  The message id.
     *
     * @return mixed  The message body or a PEAR error in case of an error.
     */
    public function getMessageBody($mailbox, $uid)
    {
        $options = array('ids' => array($uid));
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->bodyText();

        $result = $this->_imap->fetch($mailbox, $query, $options);
        return $result[$uid]['bodytext'][0];
    }

}
