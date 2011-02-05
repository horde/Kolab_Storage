<?php
/**
 * An Kolab storage mock driver.
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
 * An Kolab storage mock driver.
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
class Horde_Kolab_Storage_Driver_Mock
extends Horde_Kolab_Storage_Driver_Base
{
    /**
     * The data of the mailboxes.
     *
     * @var array
     */
    private $_data;

    /**
     * The regular expression for converting folder names.
     *
     * @var string
     */
    private $_conversion_pattern;

    /**
     * The data of the mailbox currently opened
     *
     * @var array
     */
    private $_mbox = null;

    /**
     * The name of the mailbox currently opened
     *
     * @var array
     */
    private $_mboxname = null;

    /**
     * Constructor.
     *
     * @param Horde_Kolab_Storage_Factory $factory A factory for helper objects.
     * @param array $params                        Connection parameters.
     */
    public function __construct(
        Horde_Kolab_Storage_Factory $factory,
        $params = array()
    ) {
        if (isset($params['data'])) {
            $this->_data = $params['data'];
            unset($params['data']);
        } else {
            $this->_data = array();
        }
        parent::__construct($factory, $params);
    }

    /**
     * Convert the external folder id to an internal mailbox name.
     *
     * @param string $folder The external folder name.
     *
     * @return string The internal mailbox id.
     */
    private function _convertToInternal($folder)
    {
        if (substr($folder, 0, 5) == 'INBOX') {
            $user = explode('@', $this->getAuth());
            return 'user/' . $user[0] . substr($folder, 5);
        }
        return $folder;
    }

    /**
     * Convert the internal mailbox name into an external folder id.
     *
     * @param string $mbox The internal mailbox name.
     *
     * @return string The external folder id.
     */
    private function _convertToExternal($mbox)
    {
        if ($this->_conversion_pattern === null) {
            $user = explode('@', $this->getAuth());
            $this->_conversion_pattern = '#^user/' . $user[0] . '#';
        }
        return preg_replace($this->_conversion_pattern, 'INBOX', $mbox);;
    }

    /**
     * Create the backend driver.
     *
     * @return mixed The backend driver.
     */
    public function createBackend()
    {
    }

    /**
     * Return the unique connection id.
     *
     * @return string The connection id.
     */
    public function getId()
    {
        return $this->getAuth() . '@mock:0';
    }

    /**
     * Retrieves a list of mailboxes on the server.
     *
     * @return array The list of mailboxes.
     */
    public function getMailboxes()
    {
        $result = array();
        foreach (array_keys($this->_data) as $mbox) {
            $result[] = $this->_convertToExternal($mbox);
        }
        return $result;
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
        $folder = $this->_convertToInternal($folder);
        if (isset($this->_data[$folder])) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf("IMAP folder %s does already exist!", $folder)
            );
        }
        $this->_data[$folder] = array(
            'status' => array(
                'uidvalidity' => time(),
                'uidnext' => 1),
            'mails' => array(),
            'permissions' => array($this->getAuth() => 'lrswipkxtecda'),
            'annotations' => array(),
        );
    }

    /**
     * Delete the specified folder.
     *
     * @param string $folder The folder to delete.
     *
     * @return NULL
     */
    public function delete($folder)
    {
        $folder = $this->_convertToInternal($folder);
        if (!isset($this->_data[$folder])) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf("IMAP folder %s does not exist!", $folder)
            );
        }
        unset($this->_data[$folder]);
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
        $old = $this->_convertToInternal($old);
        $new = $this->_convertToInternal($new);
        if (!isset($this->_data[$old])) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf("IMAP folder %s does not exist!", $old)
            );
        }
        if (isset($this->_data[$new])) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf("IMAP folder %s does already exist!", $new)
            );
        }
        $this->_data[$new] = $this->_data[$old];
        unset($this->_data[$old]);
    }

    /**
     * Does the backend support ACL?
     *
     * @return boolean True if the backend supports ACLs.
     */
    public function hasAclSupport()
    {
        return true;
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
        $folder = $this->_convertToInternal($folder);
        $this->_failOnMissingFolder($folder);
        if (isset($this->_data[$folder]['permissions'])) {
            return $this->_data[$folder]['permissions'];
        }
        return array();
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
        $folder = $this->_convertToInternal($folder);
        $this->_failOnMissingFolder($folder);
        if (isset($this->_data[$folder]['permissions'][$this->getAuth()])) {
            return $this->_data[$folder]['permissions'][$this->getAuth()];
        }
        return '';
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
        $folder = $this->_convertToInternal($folder);
        $this->_failOnMissingFolder($folder);
        $this->_data[$folder]['permissions'][$user] = $acl;
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
        $folder = $this->_convertToInternal($folder);
        $this->_failOnMissingFolder($folder);
        if (isset($this->_data[$folder]['permissions'][$user])) {
            unset($this->_data[$folder]['permissions'][$user]);
        }
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
        $result = array();
        foreach (array_keys($this->_data) as $mbox) {
            if (isset($this->_data[$mbox]['annotations'][$annotation])) {
                $result[$this->_convertToExternal($mbox)] = $this->_data[$mbox]['annotations'][$annotation];
            }
        }
        return $result;
    }

    /**
     * Fetches the annotation on a folder.
     *
     * @param string $entry         The entry to fetch.
     * @param string $mailbox_name  The name of the folder.
     *
     * @return mixed  The annotation value or a PEAR error in case of an error.
     */
    public function getAnnotation($entry, $mailbox_name)
    {
        $mailbox_name = $this->_convertToInternal($mailbox_name);
        $old_mbox = null;
        if ($mailbox_name != $this->_mboxname) {
            $old_mbox = $this->_mboxname;
            $result = $this->select($mailbox_name);
            if (is_a($result, 'PEAR_Error')) {
                return $result;
            }
        }
        if (!isset($this->_mbox['annotations'][$entries])
            || !isset($this->_mbox['annotations'][$entries][$value])) {
            return false;
        }
        $annotation = $this->_mbox['annotations'][$entries][$value];
        if ($old_mbox) {
            $this->select($old_mbox);
        }
        return $annotation;
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
        $mailbox = $this->_convertToInternal($mailbox);
        $this->_failOnMissingFolder($mailbox);
        $this->_data[$mailbox]['annotations'][$annotation] = $value;
    }

    /**
     * Error out in case the provided folder is missing.
     *
     * @param string $folder The folder.
     *
     * @return NULL
     *
     * @throws Horde_Kolab_Storage_Exception In case the folder is missing.
     */
    private function _failOnMissingFolder($folder)
    {
        if (!isset($this->_data[$folder])) {
            throw new Horde_Kolab_Storage_Exception(
                sprintf('The folder %s does not exist!', $folder)
            );
        }
    }
    


    /**
     * Opens the given folder.
     *
     * @param string $folder  The folder to open
     *
     * @return mixed  True in case the folder was opened successfully, a PEAR
     *                error otherwise.
     */
    public function select($folder)
    {
        $folder = $this->_convertToInternal($folder);
        if (!isset($GLOBALS['KOLAB_TESTING'][$folder])) {
            throw new Horde_Kolab_Storage_Exception(sprintf("IMAP folder %s does not exist!", $folder));
        }
        $this->_mbox = &$GLOBALS['KOLAB_TESTING'][$folder];
        $this->_mboxname = $folder;
        return true;
    }

    /**
     * Does the given folder exist?
     *
     * @param string $folder The folder to check.
     *
     * @return boolean True in case the folder exists, false otherwise.
     */
    public function exists($folder)
    {
        //@todo: make faster by directly accessing the _data array.
        $folders = $this->getMailboxes();
        if (in_array($folder, $folders)) {
            return true;
        }
        return false;
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
        return $this->_imap->status($folder,
                                    Horde_Imap_Client::STATUS_UIDNEXT
                                    | Horde_Imap_Client::STATUS_UIDVALIDITY);
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
        $search_query = new Horde_Imap_Client_Search_Query();
        $search_query->flag('DELETED', false);
        $uidsearch = $this->_imap->search($folder, $search_query);
        $uids = $uidsearch['match'];
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
        $criteria = array(
            Horde_Imap_Client::FETCH_HEADERTEXT => array(
                array(
                )
            )
        );
        $result = $this->_imap->fetch($mailbox, $criteria, $options);
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
        $criteria = array(
            Horde_Imap_Client::FETCH_BODYTEXT => array(
                array(
                )
            )
        );
        $result = $this->_imap->fetch($mailbox, $criteria, $options);
        return $result[$uid]['bodytext'][0];
    }


}