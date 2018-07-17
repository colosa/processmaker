<?php

class EntityFacetItem extends EntityBase
{
    public $facetName = '';
    public $facetPrintName = '';
    public $facetCount = '';
    public $facetSelectCondition = ''; // selected condition used to select

    // this facet
    private function __construct()
    {
    }

    public static function createEmpty()
    {
        $obj = new EntityFacetItem();
        return $obj;
    }

    public static function createForInsert($data)
    {
        $obj = new EntityFacetItem();

        $obj->initializeObject($data);

        $requiredFields = array(
            "facetName",
            "facetCount"
        );

        $obj->validateRequiredFields($requiredFields);

        return $obj;
    }
}
