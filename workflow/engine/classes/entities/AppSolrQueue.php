<?php
require_once ('Base.php');

/**
 * Application Solr Queue
 */
class Entity_AppSolrQueue extends Entity_Base
{
    public $appUid = '';
    public $appUpdated = 0;

    private function __construct ()
    {

    }

    static function createEmpty ()
    {
        $obj = new Entity_AppSolrQueue();
        return $obj;
    }

    static function createForRequest ($data)
    {
        $obj = new Entity_AppSolrQueue();

        $obj->initializeObject( $data );

        $requiredFields = array ("appUid","appUpdated"
        );

        $obj->validateRequiredFields( $requiredFields );

        return $obj;
    }
}

