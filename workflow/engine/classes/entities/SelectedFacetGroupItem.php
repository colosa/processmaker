<?php
require_once ('Base.php');

class Entity_SelectedFacetGroupItem extends Entity_Base
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
  
  static function createEmpty()
  {
    $obj = new Entity_SelectedFacetGroupItem ();
    return $obj;
  }
  
  static function createForRequest($data)
  {
    $obj = new Entity_SelectedFacetGroupItem ();
    
    $obj->initializeObject ($data);
    
    $requiredFields = array (
        "selectedFacetGroupName",
        "selectedFacetItemName" 
    );
    
    $obj->validateRequiredFields ($requiredFields);
    
    return $obj;
  }

}