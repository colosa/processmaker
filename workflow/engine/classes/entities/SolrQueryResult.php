<?php
require_once ('Base.php');

class Entity_SolrQueryResult extends Entity_Base
{
    public $sEcho = '';
    public $iTotalRecords = 0;
    public $iTotalDisplayRecords = 10;
    public $aaData = array (); // array of arrays of records to

    // display


    private function __construct ()
    {
    }

    static function createEmpty ()
    {
        $obj = new Entity_SolrQueryResult();
        return $obj;
    }

    static function createForRequest ($data)
    {
        $obj = new Entity_SolrQueryResult();

        $obj->initializeObject( $data );

        $requiredFields = array ('sEcho','iTotalRecords','iTotalDisplayRecords','aaData'
        );

        $obj->validateRequiredFields( $requiredFields );

        return $obj;
    }
}

