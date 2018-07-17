<?php

class EntitySelectedFacetGroupItem extends EntityBase
{
    public $selectedFacetGroupName = '';
    public $selectedFacetGroupPrintName = '';
    public $selectedFacetItemName = '';
    public $selectedFacetItemPrintName = '';
    public $selectedFacetRemoveCondition = ''; // remove condition, string of
    // selected facets without this
    // facet
  
    private function __construct()
    {
    }
  
    public static function createEmpty()
    {
        $obj = new EntitySelectedFacetGroupItem();
        return $obj;
    }
  
    public static function createForRequest($data)
    {
        $obj = new EntitySelectedFacetGroupItem();
    
        $obj->initializeObject($data);
    
        $requiredFields = array(
        "selectedFacetGroupName",
        "selectedFacetItemName"
    );
    
        $obj->validateRequiredFields($requiredFields);
    
        return $obj;
    }
}
