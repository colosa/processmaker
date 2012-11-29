<?php
//require_once ('Base.php');

class Entity_FacetRequest extends Entity_Base
{
  public $workspace = '';
  public $searchText = '';
  public $facetFields = array ();
  public $facetQueries = array ();
  public $facetDates = array ();
  public $facetDatesStart = '';
  public $facetDatesEnd = '';
  public $facetDateGap = '';
  public $facetRanges = array ();
  public $filters = array ();
  public $selectedFacetsString = '';
  
  private function __construct()
  {
  }
  
  static function createEmpty()
  {
    $obj = new Entity_FacetRequest ();
    return $obj;
  }
  
  static function createForRequest($data)
  {
    $obj = new Entity_FacetRequest ();
    
    $obj->initializeObject ($data);
    
    $requiredFields = array (
        "workspace" 
    );
    
    $obj->validateRequiredFields ($requiredFields);
    
    return $obj;
  }

}