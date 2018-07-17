<?php

class EntityFacetInterfaceResult extends EntityBase
{
    // array of facetsgroups, array of Entity_SelectedFacetGroupItem, filter text
    public $aFacetGroup = array();
    public $aSelectedFacetGroupItem = array();
    public $sFilterText = '';

    private function __construct()
    {
    }

    public static function createEmpty()
    {
        $obj = new EntityFacetInterfaceResult();
        return $obj;
    }

    public static function createForRequest($data)
    {
        $obj = new EntityFacetInterfaceResult();

        $obj->initializeObject($data);

        $requiredFields = array(
            "aFacetGroup",
            "aSelectedFacetGroupItem",
            "sFilterText"
        );

        $obj->validateRequiredFields($requiredFields);

        return $obj;
    }
}
