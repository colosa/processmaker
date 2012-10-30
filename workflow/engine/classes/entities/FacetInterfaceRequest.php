<?php
require_once ('Base.php');

class Entity_FacetInterfaceRequest extends Entity_Base
{
    public $searchText = '';
    public $selectedFacetsString = ''; // string of selected facet groups and

    // items in format:
    // groupkey1::groupdesc1:::itemkey1::itemdesc1,groupkey2::groupdesc2:::itemkey2::itemdesc2,
    // groupkey3::groupdesc3:::itemkey3::itemdesc3
    // var $selectedFacetFields = array();
    // var $selectedFacetTypes = array();


    private function __construct ()
    {
    }

    static function createEmpty ()
    {
        $obj = new Entity_FacetInterfaceRequest();
        return $obj;
    }

    static function createForRequest ($data)
    {
        $obj = new Entity_FacetInterfaceRequest();

        $obj->initializeObject( $data );

        $requiredFields = array ("searchText","selectedFacetsString"
        );

        $obj->validateRequiredFields( $requiredFields );

        return $obj;
    }
}

