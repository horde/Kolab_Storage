<?php
namespace Horde\Kolab\Storage\Test\Stub;
use Horde_Kolab_Storage_Driver;
use Horde_Kolab_Storage_Data_Parser;
use Horde_Kolab_Storage_Folder_Stamp;
use Horde_Kolab_Storage_Driver_Namespace;
use Horde_Mime_Headers;
use Horde_Mime_Part;

class Driver
implements Horde_Kolab_Storage_Driver
{
    public $messages;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function setMessage($folder, $id, $message)
    {
        $this->messages[$folder][$id] = $message;
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
     * Returns the actual backend driver.
     *
     * If there is no driver set the driver should be constructed within this
     * method.
     *
     * @return mixed The backend driver.
     */
    public function getBackend()
    {
    }


    /**
     * Set the backend driver.
     *
     * @param mixed $backend The driver that should be used.
     *
     * @return NULL
     */
    public function setBackend($backend)
    {
    }


    /**
     * Returns the parser for data objects.
     *
     * @return Horde_Kolab_Storage_Data_Parser The parser.
     */
    public function getParser()
    {
    }


    /**
     * Set the data parser.
     *
     * @param mixed $parser The parser that should be used.
     *
     * @return NULL
     */
    public function setParser(Horde_Kolab_Storage_Data_Parser $parser)
    {
    }


    /**
     * Return the id of the user currently authenticated.
     *
     * @return string The id of the user that opened the connection.
     */
    public function getAuth()
    {
        return $this->user;
    }


    /**
     * Return the unique connection id.
     *
     * @return string The connection id.
     */
    public function getId()
    {
    }


    /**
     * Return the connection parameters.
     *
     * @return array The connection parameters.
     */
    public function getParameters()
    {
    }


    /**
     * Checks if the backend supports CATENATE.
     *
     * @return boolean True if the backend supports CATENATE.
     */
    public function hasCatenateSupport()
    {
    }



    /** List functionality */

    /**
     * Retrieves a list of folders from the server.
     *
     * @return array The list of folders.
     */
    public function listFolders()
    {
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
    }


    /**
     * Does the backend support ACL?
     *
     * @return boolean True if the backend supports ACLs.
     */
    public function hasAclSupport()
    {
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
    }


    /**
     * Retrieves the specified annotation for the complete list of folders.
     *
     * @param string $annotation The name of the annotation to retrieve.
     *
     * @return array An associative array combining the folder names as key with
     * the corresponding annotation value.
     */
    public function listAnnotation($annotation)
    {
    }


    /**
     * Fetches the annotation from a folder.
     *
     * @param string $folder     The name of the folder.
     * @param string $annotation The annotation to get.
     *
     * @return string The annotation value.
     */
    public function getAnnotation($folder, $annotation)
    {
    }


    /**
     * Sets the annotation on a folder.
     *
     * @param string $folder     The name of the folder.
     * @param string $annotation The annotation to set.
     * @param array  $value      The values to set
     *
     * @return NULL
     */
    public function setAnnotation($folder, $annotation, $value)
    {
    }


    /**
     * Retrieve the namespace information for this connection.
     *
     * @return Horde_Kolab_Storage_Driver_Namespace The initialized namespace handler.
     */
    public function getNamespace()
    {
    }




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
    public function getStamp($folder)
    {
    }


    /**
     * Returns the status of the current folder.
     *
     * @param string $folder Check the status of this folder.
     *
     * @return array An array that contains 'uidvalidity' and 'uidnext'.
     */
    public function status($folder)
    {
    }


    /**
     * Returns the message ids of the messages in this folder.
     *
     * @param string $folder Check the status of this folder.
     *
     * @return array The message ids.
     */
    public function getUids($folder)
    {
    }


    /**
     * Fetches the objects for the specified UIDs.
     *
     * @param string  $folder  The folder to access.
     * @param array   $uids    The message UIDs.
     * @param array   $options Additional options.
     * <pre>
     *  - type    - (string) The data type.
     *  - version - (int)    The format version.
     *  - raw     - (bool)   Should the raw data be returned? 
     * </pre>
     *
     * @return array The objects.
     */
    public function fetch($folder, $uids, $options = array())
    {
    }


    /**
     * Retrieves the messages for the given message ids.
     *
     * @param string $folder The folder to fetch the messages from.
     * @param array  $uids   The message UIDs.
     *
     * @return array An array of message structures parsed into Horde_Mime_Part
     *               instances.
     */
    public function fetchStructure($folder, $uids)
    {
    }


    /**
     * Retrieves a bodypart for the given message ID and mime part ID.
     *
     * @param string $folder The folder to fetch the messages from.
     * @param array  $uid    The message UID.
     * @param array  $id     The mime part ID.
     *
     * @return resource  The body part, as a stream resource.
     */
    public function fetchBodypart($folder, $uid, $id)
    {
        $message = Horde_Mime_Part::parseMessage($this->messages[$folder][$uid]);
        return $message->getPart($id)->getContents(array('stream' => true));
    }


    /**
     * Retrieves a complete message.
     *
     * @param string $folder The folder to fetch the messages from.
     * @param array  $uid    The message UID.
     *
     * @return array The message encapsuled as an array that contains a
     *               Horde_Mime_Headers and a Horde_Mime_Part object.
     */
    public function fetchComplete($folder, $uid)
    {
        return array(
            Horde_Mime_Headers::parseHeaders($this->messages[$folder][$uid]),
            Horde_Mime_Part::parseMessage($this->messages[$folder][$uid])
        );
    }

    public function fetchHeaders($folder, $uid)
    {
        return Horde_Mime_Headers::parseHeaders($this->messages[$folder][$uid]);
    }

    /**
     * Appends a message to the given folder.
     *
     * @param string   $folder  The folder to append the message(s) to.
     * @param resource $msg     The message to append.
     *
     * @return mixed True or the UID of the new message in case the backend
     *               supports UIDPLUS.
     */
    public function appendMessage($folder, $msg)
    {
        rewind($msg);
        $this->messages[$folder][] = stream_get_contents($msg);
        return count($this->messages[$folder]) - 1;
    }

    /**
     * Deletes messages from the specified folder.
     *
     * @param string  $folder  The folder to delete messages from.
     * @param integer $uids    IMAP message ids.
     *
     * @return NULL
     */
    public function deleteMessages($folder, $uids)
    {
    }


    /**
     * Moves a message to a new folder.
     *
     * @param integer $uid         IMAP message id.
     * @param string  $old_folder  Source folder.
     * @param string  $new_folder  Target folder.
     *
     * @return NULL
     */
    public function moveMessage($uid, $old_folder, $new_folder)
    {
    }


    /**
     * Expunges messages in the current folder.
     *
     * @param string $folder The folder to expunge.
     *
     * @return NULL
     */
    public function expunge($folder)
    {
    }

}
