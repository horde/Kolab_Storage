<?php
/**
 * The driver definition for accessing Kolab storage.
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
 * The driver definition for accessing Kolab storage.
 *
 * Copyright 2004-2011 The Horde Project (http://www.horde.org/)
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
interface Horde_Kolab_Storage_Driver
{
    /** Generic functionality */

    /**
     * Create the backend driver.
     *
     * @return mixed The backend driver.
     */
    public function createBackend();

    /**
     * Returns the actual backend driver.
     *
     * If there is no driver set the driver should be constructed within this
     * method.
     *
     * @return mixed The backend driver.
     */
    public function getBackend();

    /**
     * Set the backend driver.
     *
     * @param mixed $backend The driver that should be used.
     *
     * @return NULL
     */
    public function setBackend($backend);

    /**
     * Returns the parser for data objects.
     *
     * @return Horde_Kolab_Storage_Data_Parser The parser.
     */
    public function getParser();

    /**
     * Set the data parser.
     *
     * @param mixed $parser The parser that should be used.
     *
     * @return NULL
     */
    public function setParser(Horde_Kolab_Storage_Data_Parser $parser);

    /**
     * Return the id of the user currently authenticated.
     *
     * @return string The id of the user that opened the connection.
     */
    public function getAuth();

    /**
     * Return the unique connection id.
     *
     * @return string The connection id.
     */
    public function getId();

    /**
     * Return the connection parameters.
     *
     * @return array The connection parameters.
     */
    public function getParameters();


    /** List functionality */

    /**
     * Retrieves a list of mailboxes from the server.
     *
     * @return array The list of mailboxes.
     */
    public function getMailboxes();

    /**
     * Create the specified folder.
     *
     * @param string $folder The folder to create.
     *
     * @return NULL
     */
    public function create($folder);

    /**
     * Delete the specified folder.
     *
     * @param string $folder  The folder to delete.
     *
     * @return NULL
     */
    public function delete($folder);

    /**
     * Rename the specified folder.
     *
     * @param string $old  The folder to rename.
     * @param string $new  The new name of the folder.
     *
     * @return NULL
     */
    public function rename($old, $new);

    /**
     * Does the backend support ACL?
     *
     * @return boolean True if the backend supports ACLs.
     */
    public function hasAclSupport();

    /**
     * Retrieve the access rights for a folder.
     *
     * @param string $folder The folder to retrieve the ACL for.
     *
     * @return array An array of rights.
     */
    public function getAcl($folder);

    /**
     * Retrieve the access rights the current user has on a folder.
     *
     * @param string $folder The folder to retrieve the user ACL for.
     *
     * @return string The user rights.
     */
    public function getMyAcl($folder);

    /**
     * Set the access rights for a folder.
     *
     * @param string $folder  The folder to act upon.
     * @param string $user    The user to set the ACL for.
     * @param string $acl     The ACL.
     *
     * @return NULL
     */
    public function setAcl($folder, $user, $acl);

    /**
     * Delete the access rights for user on a folder.
     *
     * @param string $folder  The folder to act upon.
     * @param string $user    The user to delete the ACL for
     *
     * @return NULL
     */
    public function deleteAcl($folder, $user);

    /**
     * Retrieves the specified annotation for the complete list of mailboxes.
     *
     * @param string $annotation The name of the annotation to retrieve.
     *
     * @return array An associative array combining the folder names as key with
     * the corresponding annotation value.
     */
    public function listAnnotation($annotation);

    /**
     * Fetches the annotation from a folder.
     *
     * @param string $mailbox    The name of the folder.
     * @param string $annotation The annotation to get.
     *
     * @return string The annotation value.
     */
    public function getAnnotation($mailbox, $annotation);

    /**
     * Sets the annotation on a folder.
     *
     * @param string $mailbox    The name of the folder.
     * @param string $annotation The annotation to set.
     * @param array  $value      The values to set
     *
     * @return NULL
     */
    public function setAnnotation($mailbox, $annotation, $value);

    /**
     * Retrieve the namespace information for this connection.
     *
     * @return Horde_Kolab_Storage_Driver_Namespace The initialized namespace handler.
     */
    public function getNamespace();



    /** Data functionality */

    /**
     * Returns a stamp for the current folder status. This stamp can be used to
     * identify changes in the folder data.
     *
     * @param string $folder Return the stamp for this folder.
     *
     * @return Horde_Kolab_Storage_Folder_Stamp A stamp indicating the current
     *                                          folder status.
     */
    public function getStamp($folder);




    /**
     * Appends a message to the current folder.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     * @param string $msg     The message to append.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function appendMessage($mailbox, $msg);

    /**
     * Deletes messages from the current folder.
     *
     * @param integer $uids  IMAP message ids.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function deleteMessages($mailbox, $uids);

    /**
     * Moves a message to a new folder.
     *
     * @param integer $uid        IMAP message id.
     * @param string $new_folder  Target folder.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function moveMessage($old_folder, $uid, $new_folder);

    /**
     * Expunges messages in the current folder.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     *
     * @return mixed  True or a PEAR error in case of an error.
     */
    public function expunge($mailbox);

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
    public function getMessageHeader($mailbox, $uid, $peek_for_body = true);

    /**
     * Retrieves the message body for a given message id.
     *
     * @param string $mailbox The mailbox to append the message(s) to. Either
     *                        in UTF7-IMAP or UTF-8.
     * @param integet $uid  The message id.
     *
     * @return mixed  The message body or a PEAR error in case of an error.
     */
    public function getMessageBody($mailbox, $uid);
}