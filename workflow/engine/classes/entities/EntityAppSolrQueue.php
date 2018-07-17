<?php

class EntityAppSolrQueue extends EntityBase
{
    public $appUid = '';
    public $appChangeDate = '';
    public $appChangeTrace = '';
    public $appUpdated = 0;

    private function __construct()
    {
    }

    public static function createEmpty()
    {
        $obj = new EntityAppSolrQueue();
        return $obj;
    }

    public static function createForRequest($data)
    {
        $obj = new EntityAppSolrQueue();

        $obj->initializeObject($data);

        $requiredFields = array(
            "appUid",
            "appChangeDate",
            "appChangeTrace",
            "appUpdated"
        );

        $obj->validateRequiredFields($requiredFields);

        return $obj;
    }
}
