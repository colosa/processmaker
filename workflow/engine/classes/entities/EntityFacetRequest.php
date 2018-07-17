<?php

class EntityFacetRequest extends EntityBase
{
    public $workspace = '';
    public $searchText = '';
    public $facetFields = array();
    public $facetQueries = array();
    public $facetDates = array();
    public $facetDatesStart = '';
    public $facetDatesEnd = '';
    public $facetDateGap = '';
    public $facetRanges = array();
    public $filters = array();
    public $selectedFacetsString = '';
  
    private function __construct()
    {
    }
  
    public static function createEmpty()
    {
        $obj = new EntityFacetRequest();
        return $obj;
    }
  
    public static function createForRequest($data)
    {
        $obj = new EntityFacetRequest();
    
        $obj->initializeObject($data);
    
        $requiredFields = array(
        "workspace"
    );
    
        $obj->validateRequiredFields($requiredFields);
    
        return $obj;
    }
}
