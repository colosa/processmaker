<?php

//require_once ('Base.php');

class Entity_SolrRequestData extends Entity_Base
{

    public $workspace = '';
    public $startAfter = 0;
    public $pageSize = 10;
    public $searchText = '*:*';
    public $filterText = ''; // comma separated list of filters field:value
    public $numSortingCols = 0; // number of columns that are sorted
    public $sortableCols = array(); // array of booleans indicating if column is
    // sortable (true, false)
    public $sortCols = array(); // array of indices of sorted columns index
    // based in the total number of sorting cols
    public $sortDir = array(); // array of direction of sorting for each
    // column (desc, asc)
    public $includeCols = array();
    public $resultFormat = 'xml'; // json, xml, php

    private function __construct()
    {
    }

    public static function createEmpty()
    {
        $obj = new Entity_SolrRequestData ();
        return $obj;
    }

    public static function createForRequestPagination($data)
    {
        $obj = new Entity_SolrRequestData ();

        $obj->initializeObject($data);

        $requiredFields = array(
            'workspace'
        );

        $obj->validateRequiredFields($requiredFields);

        return $obj;
    }
}
 