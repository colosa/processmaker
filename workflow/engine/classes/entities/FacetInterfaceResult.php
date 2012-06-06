<?php
require_once ('Base.php');

class Entity_FacetInterfaceResult extends Entity_Base
{
  // array of facetsgroups, array of Entity_SelectedFacetGroupItem, filter text
  
  public $aFacetGroup = array ();
  public $aSelectedFacetGroupItem = array ();
  public $sFilterText = '';
  
  private function __construct()
  {
  }
  
  static function createEmpty()
  {
    $obj = new Entity_FacetInterfaceResult ();
    return $obj;
  }
  
  static function createForRequest($data)
  {
    $obj = new Entity_FacetInterfaceResult ();
    
    $obj->initializeObject ($data);
    
    $requiredFields = array (
        "aFacetGroup",
        "aSelectedFacetGroupItem",
        "sFilterText" 
    );
    
    $obj->validateRequiredFields ($requiredFields);
    
    return $obj;
  }

}