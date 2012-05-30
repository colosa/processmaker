<?php
require_once ('Base.php');

/**
 * Facet group entity that represent a facet group
 *
 * @property $facetGroupName: The name of the facet (field name in solr index)
 * @property $facetGroupPrintName: The print name of the facet (Human readable
 *           description)
 * @property $facetGroupType: The type of facet group, field, daterange, filter,
 *           range
 * @property $facetGroupId: An identifier to find group information
 * @property $facetItems: array of facet items
 * @author dev-HebertSaak
 *        
 */
class Entity_FacetGroup extends Entity_Base
{
  public $facetGroupName = '';
  public $facetGroupPrintName = '';
  public $facetGroupType = ''; // field, daterange, query
  public $facetGroupId = '';
  public $facetItems = array ();
  
  private function __construct()
  {
  }
  
  static function createEmpty()
  {
    $obj = new Entity_FacetGroup ();
    return $obj;
  }
  
  static function createForInsert($data)
  {
    $obj = new Entity_FacetGroup ();
    
    $obj->initializeObject ($data);
    
    $requiredFields = array (
        "facetGroupName",
        "facetItems" 
    );
    
    $obj->validateRequiredFields ($requiredFields);
    
    return $obj;
  }

}