<?php
//require_once ('Base.php');

class Entity_FacetResult extends Entity_Base
{
    public $aFacetGroups = array ();
    public $aSelectedFacetGroups = array ();
    public $sFilterText = '';

    private function __construct ()
    {
    }

    static function createEmpty ()
    {
        $obj = new Entity_FacetResult();
        return $obj;
    }

    static function createForRequest ($data)
    {
        $obj = new Entity_FacetResult();

        $obj->initializeObject( $data );

        $requiredFields = array ("aFacetGroups","aSelectedFacetGroups","sFilterText"
        );

        $obj->validateRequiredFields( $requiredFields );

        return $obj;
    }
}

