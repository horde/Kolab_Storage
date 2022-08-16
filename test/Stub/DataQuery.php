<?php
namespace Horde\Kolab\Storage\Test\Stub;
use Horde_Kolab_Storage_Data_Query;
use Horde_Log_Logger;

class DataQuery implements Horde_Kolab_Storage_Data_Query
{
    public $synchronized = false;

    /**
     * Synchronize the query data with the information from the backend.
     *
     * @param array $params Additional parameters.
     *
     * @return void
     */
    public function synchronize($params = array()): void
    {
        $this->synchronized = true;
    }

    public function setLogger(Horde_Log_Logger $logger): void
    {

    }
}