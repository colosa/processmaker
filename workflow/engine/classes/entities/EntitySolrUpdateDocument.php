<?php

class EntitySolrUpdateDocument extends EntityBase
{
    public $workspace = '';
    public $document = '';
  
    private function __construct()
    {
    }
  
    public static function createEmpty()
    {
        $obj = new EntitySolrUpdateDocument();
        return $obj;
    }
  
    public static function createForRequest($data)
    {
        $obj = new EntitySolrUpdateDocument();
    
        $obj->initializeObject($data);
    
        $requiredFields = array(
        "workspace",
        "document"
    );
    
        $obj->validateRequiredFields($requiredFields);
    
        return $obj;
    }
}
