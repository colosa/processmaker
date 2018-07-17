<?php

class EntityFacetResult extends EntityBase
{
    public $aFacetGroups = array();
    public $aSelectedFacetGroups = array();
    public $sFilterText = '';
  
    private function __construct()
    {
    }
  
    public static function createEmpty()
    {
        $obj = new EntityFacetResult();
        return $obj;
    }
  
    public static function createForRequest($data)
    {
        $obj = new EntityFacetResult();
    
        $obj->initializeObject($data);
    
        $requiredFields = array(
        "aFacetGroups",
        "aSelectedFacetGroups",
        "sFilterText"
    );
    
        $obj->validateRequiredFields($requiredFields);
    
        return $obj;
    }
}
