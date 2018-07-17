<?php

class EntityFacetGroup extends EntityBase
{
    public $facetGroupName = '';
    public $facetGroupPrintName = '';
    public $facetGroupType = ''; // field, daterange, query
    public $facetGroupId = '';
    public $facetItems = array();

    private function __construct()
    {
    }

    public static function createEmpty()
    {
        $obj = new EntityFacetGroup();
        return $obj;
    }

    public static function createForInsert($data)
    {
        $obj = new EntityFacetGroup();

        $obj->initializeObject($data);

        $requiredFields = array(
            "facetGroupName",
            "facetItems"
        );

        $obj->validateRequiredFields($requiredFields);

        return $obj;
    }
}
