<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
 * The Horde_Kolab_Storage_Folder_Namespace_Config class allows to configure
 * the available IMAP namespaces on the Kolab server.
 *
 * @category Kolab
 * @package  Kolab_Storage
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Folder_Namespace_Config
extends  Horde_Kolab_Storage_Folder_Namespace
{
    /**
     * The namespace configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param string $user          The current user.
     * @param array  $configuration The namespace configuration.
     */
    public function __construct($user, array $configuration)
    {
        $this->user = $user;
        $this->configuration = $configuration;
        parent::__construct($this->_initializeData());
    }

    /**
     * Initialize the namespace elements.
     *
     * @return array The namespace elements.
     */
    private function _initializeData()
    {
        $namespace = array();
        foreach ($this->configuration as $element) {
            if ($element['type'] == Horde_Kolab_Storage_Folder_Namespace::SHARED
                && isset($element['prefix'])) {
                $namespace_element = new Horde_Kolab_Storage_Folder_Namespace_Element_SharedWithPrefix(
                    $element['name'], $element['delimiter'], $this->user, $element['prefix']
                );
            } else {
                $class = 'Horde_Kolab_Storage_Folder_Namespace_Element_' . Horde_String::ucfirst($element['type']);
                if (!class_exists($class)) {
                    throw new Horde_Kolab_Storage_Exception(
                        sprintf('Unkown namespace type "%s"', $element['type'])
                    );
                }
                $namespace_element = new $class($element['name'], $element['delimiter'], $this->user);
            }
            $namespaces[] = $namespace_element;
        }
        return $namespaces;
    }

    /**
     * Serialize this object.
     *
     * @return string  The serialized data.
     */
    public function serialize()
    {
        return serialize(array($this->user, $this->configuration));
    }
    public function __serialize(): array
    {
        return [$this->user, $this->configuration];
    }

    /**
     * Reconstruct the object from serialized data.
     *
     * @param string $data  The serialized data.
     */
    public function unserialize($data)
    {
        list($this->user, $this->configuration) = @unserialize($data);
        $this->initialize($this->_initializeData());
    }
    public function __unserialize(array $data): void
    {
        list($this->user, $this->configuration) = $data;
        $this->initialize($this->_initializeData());
    }
}