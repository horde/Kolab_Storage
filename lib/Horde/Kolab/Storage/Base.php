<?php
/**
 * The basic handler for accessing data from Kolab storage.
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
 * The basic handler for accessing data from Kolab storage.
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
class Horde_Kolab_Storage_Base
implements Horde_Kolab_Storage
{
    /**
     * The master Kolab storage system.
     *
     * @var Horde_Kolab_Storage_Driver
     */
    private $_master;

    /**
     * The factory for generating additional resources.
     *
     * @var Horde_Kolab_Storage_Factory
     */
    private $_factory;

    /**
     * List instances.
     *
     * @var array
     */
    private $_lists;

    /**
     * Constructor.
     *
     * @param Horde_Kolab_Storage_Driver  $master  The primary connection driver.
     * @param Horde_Kolab_Storage_Factory $factory The factory.
.
     */
    public function __construct(
        Horde_Kolab_Storage_Driver $master,
        Horde_Kolab_Storage_Factory $factory
    ) {
        $this->_master  = $master;
        $this->_factory = $factory;
    }

    /**
     * Get the folder list object.
     *
     * @return Horde_Kolab_Storage_List The handler for the list of folders
     *                                  present in the Kolab backend.
     */
    public function getList()
    {
        if (!isset($this->_lists[$this->_master->getId()])) {
            $list = new Horde_Kolab_Storage_List_Base(
                $this->_master,
                $this->_factory
            );
            $this->addListQuery($list, Horde_Kolab_Storage_List::QUERY_BASE);
            $this->addListQuery($list, Horde_Kolab_Storage_List::QUERY_ACL);
            $this->_lists[$this->_master->getId()] = $list;
        }
        return $this->_lists[$this->_master->getId()];
    }

    /**
     * Add a list query.
     *
     * @param Horde_Kolab_Storage_List $list   The list.
     * @param string                   $type   The query type.
     * @param array                    $params Additional query parameters.
     *
     * @return NULL
     */
    public function addListQuery(Horde_Kolab_Storage_List $list, $type, $params = array())
    {
        switch ($type) {
        case Horde_Kolab_Storage_List::QUERY_SHARE:
            $class = 'Horde_Kolab_Storage_List_Query_Share_Base';
            break;
        case Horde_Kolab_Storage_List::QUERY_BASE:
            $class = 'Horde_Kolab_Storage_List_Query_List_Base';
            break;
        case Horde_Kolab_Storage_List::QUERY_ACL:
            $class = 'Horde_Kolab_Storage_List_Query_Acl_Base';
            break;
        default:
            throw new Horde_Kolab_Storage_Exception(
                sprintf('Query type %s not supported!', $type)
            );
        }
        $list->registerQuery(
            $type, $this->_factory->createListQuery($class, $list, $params)
        );
    }

    /**
     * Get a Folder object.
     *
     * @param string $folder The folder name.
     *
     * @return Horde_Kolab_Storage_Folder The Kolab folder object.
     */
    public function getFolder($folder)
    {
        return new Horde_Kolab_Storage_Folder_Base(
            $this, $this->_master, $folder
        );
    }

    /**
     * Return a data handler for accessing data in the specified
     * folder.
     *
     * @param string $folder The name of the folder.
     * @param string $type   The type of data we want to
     *                       access in the folder.
     *
     * @return Horde_Kolab_Data The data object.
     */
    public function getData($folder, $type)
    {
    }

}