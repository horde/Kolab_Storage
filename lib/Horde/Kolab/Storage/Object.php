<?php
/**
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

/**
 * Represents a single Kolab object.
 *
 * @todo Clean up _attachments mess.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Object implements ArrayAccess, Serializable
{
    /** Indicates an invalid object. */
    const TYPE_INVALID = 'INVALID';

    /** Indicates a missing Kolab data MIME part. */
    const ERROR_MISSING_KOLAB_PART = 1;
    /** Indicates an unreadable Kolab part. */
    const ERROR_INVALID_KOLAB_PART = 2;

    /** Serialization elements */
    const SERIALIZATION_DATA = 'D';
    const SERIALIZATION_ERRORS = 'E';
    const SERIALIZATION_TYPE = 'T';
    const SERIALIZATION_STRUCTURE = 'S';
    const SERIALIZATION_FOLDER = 'F';
    const SERIALIZATION_BACKENDID = 'B';
    const SERIALIZATION_MIMEPARTID = 'P';

    /**
     * The driver for accessing the backend.
     *
     * @var Horde_Kolab_Storage_Driver
     */
    protected $_driver;

    /**
     * The folder that holds the object within the backend.
     *
     * @var string
     */
    protected $_folder;

    /**
     * The object ID within the backend.
     *
     * @var string
     */
    protected $_backend_id;

    /**
     * The ID of the MIME part carrying the object data.
     *
     * @var string
     */
    protected $_mime_part_id;

    /**
     * The object type.
     *
     * @var string
     */
    protected $_type;

    /**
     * The MIME headers of the object envelope.
     *
     * @var Horde_Mime_Headers
     */
    protected $_headers;

    /**
     * The message structure.
     *
     * @var Horde_Mime_Part
     */
    protected $_structure;

    /**
     * The content string representing the object data.
     *
     * @var resource
     */
    protected $_content;

    /**
     * The object data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * The collection of parse errors (if any).
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Return the driver for accessing the backend.
     *
     * @return Horde_Kolab_Storage_Driver The driver.
     */
    protected function _getDriver()
    {
        if ($this->_driver === null) {
            throw new Horde_Kolab_Storage_Object_Exception(
                'The driver has not been set!'
            );
        }
        return $this->_driver;
    }

    /**
     * Set the driver for accessing the backend.
     *
     * @param Horde_Kolab_Storage_Driver $driver The driver.
     */
    public function setDriver(Horde_Kolab_Storage_Driver $driver)
    {
        $this->_driver = $driver;
    }

    protected function _getFolder()
    {
        if (empty($this->_folder)) {
            throw new Horde_Kolab_Storage_Object_Exception(
                'The folder containing the object has been left unspecified!'
            );
        }
        return $this->_folder;
    }

    /**
     * @since Horde_Kolab_Storage 2.1.0
     */
    public function getBackendId()
    {
        if (empty($this->_backend_id)) {
            throw new Horde_Kolab_Storage_Object_Exception(
                'The message containing the object has been left unspecified!'
            );
        }
        return $this->_backend_id;
    }

    protected function _getMimePartId()
    {
        if (empty($this->_mime_part_id)) {
            throw new Horde_Kolab_Storage_Object_Exception(
                'There is no indication which message part might contain the object data!'
            );
        }
        return $this->_mime_part_id;
    }

    /**
     * Return the object type.
     *
     * @return string The object type.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Return the MIME headers of the object envelope.
     *
     * @return Horde_Mime_Headers The MIME headers.
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {
            $this->_headers = $this->_getDriver()->fetchHeaders(
                $this->_getFolder(),
                $this->getBackendId()
            );
        }
        return $this->_headers;
    }

    /**
     * Set the content representing the object data.
     *
     * @param resource $content The object content.
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * Fetch the raw content representing the object data.
     *
     * @return resource The raw object content.
     */
    public function getContent()
    {
        if ($this->_content === null) {
            $this->_content = $this->_getDriver()->fetchBodypart(
                $this->_getFolder(),
                $this->getBackendId(),
                $this->_getMimePartId()
            );
        }
        return $this->_content;
    }

    /**
     * Return the current content value representing the object data. This call
     * does not attempt to fetch the content from the backend.
     *
     * @return resource The raw object content.
     */
    public function getCurrentContent()
    {
        return $this->_content;
    }

    /**
     * Set the object data.
     *
     * @param array $data The object data.
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        if (!isset($this->_data['uid'])) {
            $this->getUid();
        }
    }

    /**
     * Fetch the object data.
     *
     * @return array The object data.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Return the UID of the object. If no UID has been set a valid UID will be
     * autogenerated.
     *
     * @return string The object UID.
     */
    public function getUid()
    {
        if (!isset($this->_data['uid'])) {
            $this->_data['uid'] = $this->generateUid();
        }
        return $this->_data['uid'];
    }

    /**
     * Generate a unique object ID.
     *
     * @return string  The unique ID.
     */
    public function generateUid()
    {
        return strval(new Horde_Support_Uuid());
    }

    protected function addParseError($error, $message = '')
    {
        $this->_errors[$error] = $message;
    }

    public function getParseErrors()
    {
        return $this->_errors;
    }

    public function hasParseErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * Create a new object in the backend.
     *
     * @param Horde_Kolab_Storage_Folder  $folder  The folder to retrieve the
     *                                             data object from.
     * @param Horde_Kolab_Storage_Object_Writer $data The data writer.
     * @param string $type The type of object to be stored.
     *
     * @return boolean|string The return value of the append operation.
     */
    public function create(Horde_Kolab_Storage_Folder $folder,
                           Horde_Kolab_Storage_Object_Writer $data,
                           $type)
    {
        $this->_folder = $folder->getPath();
        $this->_type = $type;
        $envelope = $this->createEnvelope();
        $envelope->addPart($this->createFreshKolabPart($data->save($this)));
        if (isset($this['_attachments'])) {
            foreach ($this['_attachments'] as $name => $attachment) {
                $part = new Horde_Mime_Part();
                $part->setType($attachment['type']);
                $part->setContents($attachment['content']);
                $part->setName($name);
                $envelope->addPart($part);
            }
        }
        $envelope->buildMimeIds();
        $this->_mime_part_id = Horde_Kolab_Storage_Object_MimeType::matchMimePartToObjectType(
            $envelope, $this->getType()
        );
        return $this->_appendMessage($envelope, $this->createEnvelopeHeaders());
    }

    /**
     * Loads the object from the backend.
     *
     * @param string $backend_id                       The object ID within the
     *                                                 backend.
     * @param Horde_Kolab_Storage_Folder $folder       The folder to retrieve
     *                                                 the data object from.
     * @param Horde_Kolab_Storage_Object_Writer $data  The data parser.
     * @param Horde_Mime_Part $structure               The MIME message
     *                                                 structure of the object.
     */
    public function load($backend_id,
                         Horde_Kolab_Storage_Folder $folder,
                         Horde_Kolab_Storage_Object_Writer $data,
                         Horde_Mime_Part $structure = null)
    {
        $this->_folder = $folder->getPath();
        $this->_backend_id = $backend_id;

        $result = Horde_Kolab_Storage_Object_MimeType::matchMimePartToFolderType(
            $structure, $folder->getType()
        );

        /* No object content matching the folder type: Try fetching the header
         * and look for a Kolab type deviating from the folder type. */
        if ($result === false || $result[0] === false) {
            $result = Horde_Kolab_Storage_Object_MimeType::matchMimePartToHeaderType(
                $structure,
                $this->getHeaders()
            );
            /* Seems to have no Kolab data part: mark invalid. */
            if ($result === false || $result[0] === false) {
                $this->_type = self::TYPE_INVALID;
                $this->addParseError(self::ERROR_MISSING_KOLAB_PART);
                return;
            }
        }
        $this->_type = $result[1];
        $mime_part = $structure->getPart($result[0]);
        if (empty($mime_part)) {
            $this->_type = self::TYPE_INVALID;
            $this->addParseError(self::ERROR_MISSING_KOLAB_PART);
            return;
        }
        $this->_mime_part_id = $result[0];
        $mime_part->setContents($this->getContent(), array('encoding' => '8bit'));
        $result = $data->load($mime_part->getContents(array('stream' => true)), $this);
        if ($result instanceof Exception) {
            $this->addParseError(self::ERROR_INVALID_KOLAB_PART, $result->getMessage());
        } else {
            foreach ($structure->getParts() as $part) {
                if ($part->getMimeId() == $this->_mime_part_id ||
                    !$part->getName()) {
                    continue;
                }
                $this->_data['_attachments'][$part->getName()] = array(
                    'type' => $part->getType(),
                    'content' => $this->_getDriver()->fetchBodypart(
                            $this->_getFolder(),
                            $this->getBackendId(),
                            $part->getMimeId()
                    )
                );
            }
        }

        $this->_structure = $structure;
    }

    /**
     * Store the modified object in the backend.
     *
     * @param Horde_Kolab_Storage_Folder  $folder  The folder to retrieve the
     *                                             data object from.
     * @param Horde_Kolab_Storage_Object_Writer $data The data writer.
     * @param Horde_Mime_Part $structure The MIME message structure of the object.
     *
     * @return boolean|string The return value of the append operation.
     * @throws Horde_Kolab_Storage_Object_Exception
     */
    public function save(Horde_Kolab_Storage_Object_Writer $data)
    {
        list($headers, $body) = $this->_getDriver()->fetchComplete(
            $this->_getFolder(), $this->getBackendId()
        );
        $mime_id = Horde_Kolab_Storage_Object_MimeType::matchMimePartToObjectType(
            $body, $this->getType()
        );
        if ($mime_id === false) {
            throw new Horde_Kolab_Storage_Object_Exception(
                sprintf(
                    'Missing expected mime type (%s) in object "%s" in folder "%s"!',
                    Horde_Kolab_Storage_Object_MimeType::getMimeTypeFromObjectType($this->getType()),
                    $this->getBackendId(),
                    $this->_getFolder()
                )
            );
        }

        $this->_content = $body->getPart($mime_id)->getContents(array('stream' => true));
        $body->alterPart($mime_id, $this->createFreshKolabPart($data->save($this)));
        $body->buildMimeIds();

        // Update attachments.
        if (isset($this['_attachments'])) {
            foreach ($this['_attachments'] as $name => $attachment) {
                foreach ($body->getParts() as $part) {
                    if ($part->getName() === $name) {
                        $body->removePart($part->getMimeId());
                        break;
                    }
                }
                if (!is_null($attachment)) {
                    $part = new Horde_Mime_Part();
                    $part->setType($attachment['type']);
                    $part->setContents($attachment['content']);
                    $part->setName($name);
                    $body->addPart($part);
                }
                $body->buildMimeIds();
            }
        }

        $this->_mime_part_id = Horde_Kolab_Storage_Object_MimeType::matchMimePartToObjectType(
            $body, $this->getType()
        );
        $old_uid = $this->getBackendId();

        // Save message.
        $result = $this->_appendMessage($body, $headers);
        $this->_getDriver()->deleteMessages($this->_getFolder(), array($old_uid));
        $this->_getDriver()->expunge($this->_getFolder());
        return $result;
    }

    /**
     * Append a new message.
     *
     * @param Horde_Mime_Part $message The message.
     * @param Horde_Mime_Headers $headers The message headers.
     *
     * @return boolean|string The return value of the append operation.
     * @throws Horde_Kolab_Storage_Object_Exception
     */
    protected function _appendMessage(Horde_Mime_Part $message,
                                    Horde_Mime_Headers $headers)
    {
        $result = $this->_getDriver()->appendMessage(
            $this->_getFolder(),
            $message->toString(
                array(
                    'canonical' => true,
                    'stream' => true,
                    'headers' => $headers
                )
            )
        );
        if (is_object($result) || $result === false || $result === null) {
            throw new Horde_Kolab_Storage_Object_Exception(
                sprintf(
                    'Unexpected return value (%s) when creating an object in folder "%s"!',
                    print_r($result, true), $this->_getFolder()
                )
            );
        }
        if ($result !== true) {
            $this->_backend_id = $result;
        }
        return $result;
    }

    /**
     * Generates a new MIME messages that will wrap a Kolab groupware object.
     *
     * @return Horde_Mime_Part The new MIME message.
     */
    protected function createEnvelope()
    {
        $envelope = new Horde_Mime_Part();
        $envelope->setName('Kolab Groupware Data');
        $envelope->setType('multipart/mixed');
        $description = new Horde_Mime_Part();
        $description->setName('Kolab Groupware Information');
        $description->setType('text/plain');
        $description->setDisposition('inline');
        $description->setCharset('utf-8');
        $description->setContents(
            sprintf(
                Horde_Kolab_Storage_Translation::t(
                    "This is a Kolab Groupware object. To view this object you will need an email client that understands the Kolab Groupware format. For a list of such email clients please visit %s"
                ),
                'http://www.kolab.org/content/kolab-clients'
            ),
            array('encoding' => 'quoted-printable')
        );
        $envelope->addPart($description);
        return $envelope;
    }

    /**
     * Generate the headers for the MIME envelope of a Kolab groupware object.
     *
     * @param string $user The current user.
     *
     * @return Horde_Mime_Headers The headers for the MIME envelope.
     */
    protected function createEnvelopeHeaders()
    {
        $headers = new Horde_Mime_Headers();
        $headers->setEOL("\r\n");
        $headers->addHeader('From', $this->_getDriver()->getAuth());
        $headers->addHeader('To', $this->_getDriver()->getAuth());
        $headers->addHeader('Date', date('r'));
        $headers->addHeader('Subject', $this->getUid());
        $headers->addHeader('User-Agent', 'Horde_Kolab_Storage ' . Horde_Kolab_Storage::VERSION);
        $headers->addHeader('MIME-Version', '1.0');
        $headers->addHeader(
            'X-Kolab-Type',
            Horde_Kolab_Storage_Object_MimeType::getMimeTypeFromObjectType($this->getType())
        );
        return $headers;
    }

    /**
     * Embed the Kolab content into a new MIME Part.
     *
     * @param resource $content The Kolab content.
     *
     * @return Horde_Mime_Part The MIME part that encapsules the Kolab content.
     */
    protected function createFreshKolabPart($content)
    {
        $part = new Horde_Mime_Part();

        $part->setCharset('utf-8');
        $part->setDisposition('inline');
        $part->setDispositionParameter('x-kolab-type', 'xml');
        $part->setName('kolab.xml');

        $part->setType(
            Horde_Kolab_Storage_Object_MimeType::getMimeTypeFromObjectType($this->getType())
        );
        $part->setContents(
            $content, array('encoding' => 'quoted-printable')
        );

        return $part;
    }

    /* ArrayAccess methods. */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : '';
    }
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /* Serializable methods. */

    /**
     * Serialization.
     *
     * @return string  Serialized data.
     */
    public function serialize()
    {
        return serialize(
            $this->__serialize()
        );
    }

    public function __serialize(): array
    {
        return [
            self::SERIALIZATION_DATA => $this->_data,
            self::SERIALIZATION_ERRORS => $this->_errors,
            self::SERIALIZATION_TYPE => $this->_type,
            self::SERIALIZATION_FOLDER => $this->_folder,
            self::SERIALIZATION_BACKENDID => $this->_backend_id,
            self::SERIALIZATION_MIMEPARTID => $this->_mime_part_id,
        ];
    }

    /**
     * Unserialization.
     *
     * @param string $data  Serialized data.
     *
     * @throws Horde_Kolab_Storage_Object_Exception
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if (!is_array($data)) {
            throw new Horde_Kolab_Storage_Object_Exception('Cache data invalid');
        }
        if (isset($data[self::SERIALIZATION_DATA])) {
            $this->_data = $data[self::SERIALIZATION_DATA];
        }
        if (isset($data[self::SERIALIZATION_ERRORS])) {
            $this->_errors = $data[self::SERIALIZATION_ERRORS];
        }
        if (isset($data[self::SERIALIZATION_TYPE])) {
            $this->_type = $data[self::SERIALIZATION_TYPE];
        }
        if (isset($data[self::SERIALIZATION_FOLDER])) {
            $this->_folder = $data[self::SERIALIZATION_FOLDER];
        }
        if (isset($data[self::SERIALIZATION_BACKENDID])) {
            $this->_backend_id = $data[self::SERIALIZATION_BACKENDID];
        }
        if (isset($data[self::SERIALIZATION_MIMEPARTID])) {
            $this->_mime_part_id = $data[self::SERIALIZATION_MIMEPARTID];
        }
    }
    public function __unserialize(array $data): void
    {
        if (isset($data[self::SERIALIZATION_DATA])) {
            $this->_data = $data[self::SERIALIZATION_DATA];
        }
        if (isset($data[self::SERIALIZATION_ERRORS])) {
            $this->_errors = $data[self::SERIALIZATION_ERRORS];
        }
        if (isset($data[self::SERIALIZATION_TYPE])) {
            $this->_type = $data[self::SERIALIZATION_TYPE];
        }
        if (isset($data[self::SERIALIZATION_FOLDER])) {
            $this->_folder = $data[self::SERIALIZATION_FOLDER];
        }
        if (isset($data[self::SERIALIZATION_BACKENDID])) {
            $this->_backend_id = $data[self::SERIALIZATION_BACKENDID];
        }
        if (isset($data[self::SERIALIZATION_MIMEPARTID])) {
            $this->_mime_part_id = $data[self::SERIALIZATION_MIMEPARTID];
        }
    }
}