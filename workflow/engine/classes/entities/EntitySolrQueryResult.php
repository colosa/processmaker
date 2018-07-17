<?php

class EntitySolrQueryResult extends EntityBase
{
    public $sEcho = '';
    public $iTotalRecords = 0;
    public $iTotalDisplayRecords = 10;
    public $aaData = array(); // array of arrays of records to
    // display
  
    private function __construct()
    {
    }
  
    public static function createEmpty()
    {
        $obj = new EntitySolrQueryResult();
        return $obj;
    }
  
    public static function createForRequest($data)
    {
        $obj = new EntitySolrQueryResult();
    
        $obj->initializeObject($data);
    
        $requiredFields = array(
        'sEcho',
        'iTotalRecords',
        'iTotalDisplayRecords',
        'aaData'
    );
    
        $obj->validateRequiredFields($requiredFields);
    
        return $obj;
    }
}
