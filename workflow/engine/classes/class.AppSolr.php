<?php
/**
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2012 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 5304 Ventura Drive,
 * Delray Beach, FL, 33484, USA, or email info@colosa.com.
 *
 */

require_once ("classes" . PATH_SEP . "model" . PATH_SEP . "AppCacheView.php");
require_once "classes/model/Application.php";
require_once "classes/model/AppDelegation.php";
require_once "classes/model/AppThread.php";
require_once "classes/model/Content.php";
require_once "classes/model/Users.php";
require_once "classes/model/GroupUser.php";
require_once "classes/model/Task.php";
require_once "classes/model/TaskUser.php";
require_once "classes/model/Dynaform.php";
require_once "entities/SolrRequestData.php";
require_once "entities/SolrUpdateDocument.php";
require_once "entities/AppSolrQueue.php";
require_once "classes/model/AppSolrQueue.php";

/**
 * Invalid search text for Solr exception
 *
 * @author Herbert Saal Gutierrez
 *
 */
class InvalidIndexSearchTextException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct ($message, $code = 0)
    {
        // some code
        // make sure everything is assigned properly
        parent::__construct( $message, $code );
    }

    // custom string representation of object
    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/**
 * Application without Delegations exception
 *
 * @author Herbert Saal Gutierrez
 *
 * @category Colosa
 * @copyright Copyright (c) 2005-2012 Colosa Inc. (http://www.colosa.com)
 */
class ApplicationWithoutDelegationRecordsException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct ($message, $code = 0)
    {
        // some code
        // make sure everything is assigned properly
        parent::__construct( $message, $code );
    }

    // custom string representation of object
    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/**
 * Dynaform file corrupt
 *
 * @author Herbert Saal Gutierrez
 *
 * @category Colosa
 * @copyright Copyright (c) 2005-2012 Colosa Inc. (http://www.colosa.com)
 */
class ApplicationWithCorruptDynaformException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct ($message, $code = 0)
    {
        // some code
        // make sure everything is assigned properly
        parent::__construct( $message, $code );
    }

    // custom string representation of object
    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/**
 * Application APP_DATA could not be unserialized exception
 *
 * @author Herbert Saal Gutierrez
 *
 * @category Colosa
 * @copyright Copyright (c) 2005-2012 Colosa Inc. (http://www.colosa.com)
 */
class ApplicationAPP_DATAUnserializeException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct ($message, $code = 0)
    {
        // some code
        // make sure everything is assigned properly
        parent::__construct( $message, $code );
    }

    // custom string representation of object
    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/**
 * Implementation to display application data in the PMOS2 grids using Solr
 * search service
 *
 * @author Herbert Saal Gutierrez
 * @category Colosa
 * @copyright Copyright (c) 2005-2011 Colosa Inc. (http://www.colosa.com)
 *
 */
class AppSolr
{
    private $_solrIsEnabled = false;
    private $_solrHost = "";
    private $_solrInstance = "";
    private $debug = false; //false
    private $debugAppInfo = false;


    public function __construct ($SolrEnabled, $SolrHost, $SolrInstance)
    {
        // define solr availability
        $this->_solrIsEnabled = $SolrEnabled;
        $this->_solrHost = $SolrHost;
        $this->_solrInstance = $SolrInstance;
    }

    /**
     * Return if the Solr functionality is enabled.
     *
     * @return boolean true:enabled functionality, false:disabled functionality
     */
    public function isSolrEnabled ()
    {
        return $this->_solrIsEnabled;
    }

    /**
     * Gets the Solr string for todo cases
     *
     * Return the Solr string for todo cases
     *
     * @param string $userUid The user ID
     * @param string $filter Filter the results posible values ("read", "unread")
     * @return string Solr string
     */
    public function getToDoSolrString($userUid, $filter)
    {
        $strSolr = "APP_STATUS:TO_DO";
        $strDelgIndex = null;

        if (!empty($userUid)) {
            switch ($filter) {
                case "read":
                    $strSolr = "APP_ASSIGNED_USERS_READ:" . $userUid;
                    $strDelgIndex = "APP_ASSIGNED_USER_READ_DEL_INDEX_" . $userUid . "_txt";
                    break;
                case "unread":
                    $strSolr = "APP_ASSIGNED_USERS_UNREAD:" . $userUid;
                    $strDelgIndex = "APP_ASSIGNED_USER_UNREAD_DEL_INDEX_" . $userUid . "_txt";
                    break;
                default:
                    $strSolr = "APP_ASSIGNED_USERS:" . $userUid;
                    $strDelgIndex = "APP_ASSIGNED_USER_DEL_INDEX_" . $userUid . "_txt";
                    break;
            }
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for draft cases
     *
     * Return the Solr string for draft cases
     *
     * @param string $userUid The user ID
     * @return string Solr string
     */
    public function getDraftSolrString($userUid)
    {
        $strSolr = "APP_STATUS:DRAFT";
        $strDelgIndex = null;

        if (!empty($userUid)) {
            $strSolr = "APP_DRAFT_USER:" . $userUid;
            $strDelgIndex = "APP_DRAFT_USER_DEL_INDEX_" . $userUid . "_txt";
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for paused cases
     *
     * Return the Solr string for paused cases
     *
     * @param string $userUid The user ID
     * @return string Solr string
     */
    public function getPausedSolrString($userUid)
    {
        $strSolr = "APP_STATUS:PAUSED";
        $strDelgIndex = null;

        if (!empty($userUid)) {
            $strSolr = "APP_PAUSED_USERS:" . $userUid;
            $strDelgIndex = "APP_PAUSED_USERS_DEL_INDEX_" . $userUid . "_txt";
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for cancelled cases
     *
     * Return the Solr string for cancelled cases
     *
     * @param string $userUid The user ID
     * @return string Solr string
     */
    public function getCancelledSolrString($userUid)
    {
        $strSolr = "APP_STATUS:CANCELLED";
        $strDelgIndex = null;

        if (!empty($userUid)) {
            $strSolr = "APP_CANCELLED_USER_txt:" . $userUid;
            $strDelgIndex = "APP_CANCELLED_USER_DEL_INDEX_" . $userUid . "_txt";
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for completed cases
     *
     * Return the Solr string for completed cases
     *
     * @param string $userUid The user ID
     * @return string Solr string
     */
    public function getCompletedSolrString($userUid)
    {
        $strSolr = "APP_STATUS:COMPLETED";
        $strDelgIndex = null;

        if (!empty($userUid)) {
            $strSolr = "APP_PARTICIPATED_USERS_COMPLETED:" . $userUid;
            $strDelgIndex = "APP_PARTICIPATED_USER_COMPLETED_DEL_INDEX_" . $userUid . "_txt";
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for sent cases
     *
     * Return the Solr string for sent cases
     *
     * @param string $userUid The user ID
     * @param string $filter Filter the results posible values ("started", "completed")
     * @return string Solr string
     */
    public function getSentSolrString($userUid, $filter)
    {
        $strSolr = null;
        $strDelgIndex = null;

        if (!empty($userUid)) {
            switch ($filter) {
                case "started":
                    $strSolr = "APP_PARTICIPATED_USERS_STARTED:" . $userUid;
                    $strDelgIndex = "APP_PARTICIPATED_USER_STARTED_DEL_INDEX_" . $userUid . "_txt";
                    break;
                case "completed":
                    $strSolr = "APP_PARTICIPATED_USERS_COMPLETED:" . $userUid;
                    $strDelgIndex = "APP_PARTICIPATED_USER_COMPLETED_DEL_INDEX_" . $userUid . "_txt";
                    break;
                default:
                    $strSolr = "APP_PARTICIPATED_USERS:" . $userUid;
                    $strDelgIndex = "APP_PARTICIPATED_USER_DEL_INDEX_" . $userUid . "_txt";
                    break;
            }
        }

        return array($strSolr, $strDelgIndex);
    }

    /**
     * Gets the Solr string for all cases
     *
     * Return the Solr string for all cases
     *
     * @param string $userUid The user ID
     * @return string Solr string
     */
    public function getAllSolrString($userUid)
    {
        $strSolr = "(APP_STATUS:TO_DO OR
                     APP_STATUS:DRAFT OR
                     APP_STATUS:PAUSED OR
                     APP_STATUS:CANCELLED OR
                     APP_STATUS:COMPLETED
                    )";
        $arrayDelgIndex = array();

        if (!empty($userUid)) {
            $strSolr = "(";

            //ToDo
            $strSolr = $strSolr . "APP_ASSIGNED_USERS:" . $userUid;
            $arrayDelgIndex[] = "APP_ASSIGNED_USER_DEL_INDEX_" . $userUid . "_txt";

            $strSolr = $strSolr . " OR ";

            //Draft
            $strSolr = $strSolr . "APP_DRAFT_USER:" . $userUid;
            $arrayDelgIndex[] = "APP_DRAFT_USER_DEL_INDEX_" . $userUid . "_txt";

            $strSolr = $strSolr . " OR ";

            //Paused
            $strSolr = $strSolr . "APP_PAUSED_USERS:" . $userUid;
            $arrayDelgIndex[] = "APP_PAUSED_USERS_DEL_INDEX_" . $userUid . "_txt";

            $strSolr = $strSolr . " OR ";

            //Cancelled
            $strSolr = $strSolr . "APP_CANCELLED_USER_txt:" . $userUid;
            $arrayDelgIndex[] = "APP_CANCELLED_USER_DEL_INDEX_" . $userUid . "_txt";

            $strSolr = $strSolr . " OR ";

            //Completed
            $strSolr = $strSolr . "APP_PARTICIPATED_USERS_COMPLETED:" . $userUid;
            $arrayDelgIndex[] = "APP_PARTICIPATED_USER_COMPLETED_DEL_INDEX_" . $userUid . "_txt";

            $strSolr = $strSolr . ")";
        }

        return array($strSolr, $arrayDelgIndex);
    }

    /**
     * Gets the information of Grids using Solr server.
     *
     * Returns the list of records for the grid depending of the function
     * conditions
     * If doCount is true only the count of records is returned.
     *
     * @param string $userUid current logged user.
     * @param int $start the offset to return the group of records. Used for pagination.
     * @param int $limit The number of records to return in the set.
     * @param string $action the action: todo, participated, draft, unassigned
     * @param string $filter filter the results posible values ('read', 'unread', 'started',
     *        'completed')
     * @param string $search search string
     * @param string $process PRO_UID to filter results by specified process.
     * @param string $user USR_UID to filter results by specified user.
     * @param string $status filter by an application Status : TO_DO, COMPLETED, DRAFT
     * @param string $type default extjs
     * @param string $dateFrom filter by DEL_DELEGATE_DATE, not used
     * @param string $dateTo filter by DEL_DELEGATE_DATE, not used
     * @param string $callback default stcCallback1001 not used
     * @param string $dir sort direction ASC, DESC
     * @param string $sort sort field
     * @param boolean $doCount default=false, if true only the count of records is returned.
     * @return array return the list of cases
     */
    public function getAppGridData(
        $userUid,
        $start = null,
        $limit = null,
        $action = null,
        $filter = null,
        $search = null,
        $process = null,
        $status = null,
        $type = null,
        $dateFrom = null,
        $dateTo = null,
        $callback = null,
        $dir = null,
        $sort = "APP_CACHE_VIEW.APP_NUMBER",
        $category = null,
        $doCount = false
    ) {
        $callback = isset( $callback ) ? $callback : 'stcCallback1001';
        $dir = isset( $dir ) ? $dir : 'DESC'; // direction of sort column
        // (ASC, DESC)
        $sort = isset( $sort ) ? $sort : ''; // sort column (APP_NUMBER,
        // CASE_SUMMARY,
        // CASE_NOTES_COUNT, APP_TITLE,
        // APP_PRO_TITLE, APP_TAS_TITLE,
        // APP_DEL_PREVIOUS_USER,
        // DEL_TASK_DUE_DATE,
        // APP_UPDATE_DATE, DEL_PRIORITY)
        $start = isset( $start ) ? $start : '0';
        $limit = isset( $limit ) ? $limit : '25';
        $filter = isset( $filter ) ? $filter : ''; // posible values ('read',
        // 'unread', 'started',
        // 'completed')
        $search = isset( $search ) ? $search : ''; // search in fields, plain text
        $process = isset( $process ) ? $process : ''; // filter by an specific
        // process
        // uid
        $status = isset( $status ) ? strtoupper( $status ) : ''; // filter by an
        // specific
        // app_status
        $action = isset( $action ) ? $action : 'todo'; // todo, paused
        $type = isset( $type ) ? $type : 'extjs';
        $dateFrom = isset( $dateFrom ) ? $dateFrom : ''; // filter by
        // DEL_DELEGATE_DATE
        $dateTo = isset( $dateTo ) ? $dateTo : ''; // filter by DEL_DELEGATE_DATE

        $swErrorInSearchText = false;
        $solrQueryResult = null;
        $aPriorities = array ('1' => 'VL','2' => 'L','3' => 'N','4' => 'H','5' => 'VH'
        );
        $delegationIndexes = array ();

        $result = array ();
        $result['totalCount'] = 0;
        $result['data'] = array ();
        $result['success'] = false;
        $result['message'] = "Error description.";

        G::LoadClass( 'searchIndex' );

        try {
            if ($this->debug) {
                $this->initTimeAll = microtime( true );
            }

            // the array of data that must be returned with placeholders
            $columsToInclude = array ('APP_CREATE_DATE','','','','APP_NUMBER','','APP_PRO_TITLE','APP_STATUS','','','APP_TITLE','APP_UID','DEL_LAST_UPDATE_DATE','','','','','','','','','DEL_MAX_PRIORITY','','','','','','PRO_UID','',''
            );
            // create pagination data
            $solrSearchText = null;
            $sortableCols = array ();
            $sortCols = array ();
            $sortDir = array ();
            $numSortingCols = 0;

            // define sort conditions, default APP_NUMBER, desc
            // only one column is sorted
            $dir = strtolower( $dir );

            if (! empty( $sort )) {
                switch ($sort) {
                    case 'APP_CACHE_VIEW.APP_NUMBER':
                    case 'APP_NUMBER':
                        $sortCols[0] = 4;
                        $sortableCols[0] = 'true';
                        $sortDir[0] = $dir;
                        break;
                    // multivalue field can't be ordered
                    case 'APP_TITLE':
                        $sortCols[0] = 10;
                        $sortableCols[0] = 'true';
                        $sortDir[0] = $dir;
                        break;
                    case 'APP_PRO_TITLE':
                        $sortCols[0] = 6;
                        $sortableCols[0] = 'true';
                        $sortDir[0] = $dir;
                        break;
                    case 'APP_UPDATE_DATE':
                        $sortCols[0] = 12;
                        $sortableCols[0] = 'true';
                        $sortDir[0] = $dir;
                        break;
                    default:
                        $sortCols[0] = 4;
                        $sortableCols[0] = 'true';
                        $sortDir[0] = 'desc';
                        break;
                }
                $numSortingCols ++;
            }

            //Define conditions for the $action
            $arrayAux = array();

            switch ($action) {
                case "todo":
                    $arrayAux = $this->getToDoSolrString($userUid, $filter);
                    break;
                case "draft":
                    $arrayAux = $this->getDraftSolrString($userUid);
                    break;
                case "paused":
                    $arrayAux = $this->getPausedSolrString($userUid);
                    break;
                case "selfservice":
                case "unassigned":
                    if (!empty($userUid)) {
                        //Get the list of groups to which belongs the user.
                        $userGroups = $this->getUserGroups($userUid);

                        $solrSearchText = $solrSearchText . "(APP_UNASSIGNED_USERS:" . $userUid;

                        if (count($userGroups) > 0) {
                            $solrSearchText = $solrSearchText . " OR ";

                            foreach ($userGroups as $group) {
                                $solrSearchText = $solrSearchText . "APP_UNASSIGNED_GROUPS:" . $group["GRP_UID"] . " OR ";
                            }

                            //Remove last OR in condition
                            if ($solrSearchText != null) {
                                $solrSearchText = substr_replace($solrSearchText, null, -4);
                            }
                        }

                        $solrSearchText = $solrSearchText . ") AND ";

                        $delegationIndexes[] = "APP_UNASSIGNED_USER_GROUP_DEL_INDEX_" . $userUid . "_txt";

                        foreach ($userGroups as $group) {
                            $delegationIndexes[] = "APP_UNASSIGNED_USER_GROUP_DEL_INDEX_" . $group["GRP_UID"] . "_txt";
                        }
                    }
                    break;
                case "sent":
                    $arrayAux = $this->getSentSolrString($userUid, $filter);
                    break;
                case "search":
                    switch ($status) {
                        case "TO_DO":
                            $arrayAux = $this->getToDoSolrString($userUid, $filter);
                            break;
                        case "DRAFT":
                            $arrayAux = $this->getDraftSolrString($userUid);
                            break;
                        case "PAUSED":
                            $arrayAux = $this->getPausedSolrString($userUid);
                            break;
                        case "CANCELLED":
                            $arrayAux = $this->getCancelledSolrString($userUid);
                            break;
                        case "COMPLETED":
                            $arrayAux = $this->getCompletedSolrString($userUid);
                            break;
                        default:
                            //All status
                            $arrayAux = $this->getAllSolrString($userUid);
                            break;
                    }
                    break;
            }

            if (count($arrayAux) > 0) {
                switch ($action) {
                    case "sent":
                        if (!empty($status)) {
                            $solrSearchText = $solrSearchText . "APP_STATUS:" . $status . " AND ";
                        }
                        break;
                }

                $solrSearchText = $solrSearchText . ((!empty($arrayAux[0]))? $arrayAux[0] . " AND " : null);

                if (is_array($arrayAux[1])) {
                    foreach ($arrayAux[1] as $index => $value) {
                        $delegationIndexes[] = $value;
                    }
                } else {
                    $delegationIndexes[] = $arrayAux[1];
                }
            }

            //Get DEL_INDEX field
            $delIndexDynaField = null;

            //Process filter
            if (!empty($process)) {
                $solrSearchText = $solrSearchText . "PRO_UID:" . $process . " AND ";
            }

            //Category filter
            if (!empty($category)) {
                $solrSearchText = $solrSearchText . "PRO_CATEGORY_UID_s:" . $category . " AND ";
            }

            //search action
            if (!empty($dateFrom) && !empty($dateTo)) {
                $fromDate = date( "Y-m-d", strtotime( $dateFrom ) );
                $toDate = date( "Y-m-d", strtotime( $dateTo ) );

                $searchDateOriginal = "DEL_LAST_UPDATE_DATE:[" . $fromDate . " TO " . $toDate . "]";
                //FechaRegistro:[2011-04-15 TO 2011-04-30]


                $searchDateFormatedSolr = $this->getSearchText( $searchDateOriginal );

                $solrSearchText .= "(" . $searchDateFormatedSolr . ") AND ";

            }

            // remove last AND in condition
            if ($solrSearchText != '') {
                $solrSearchText = substr_replace( $solrSearchText, "", - 5 );
            }

            // add parenthesis to Solr search text
            if ($solrSearchText != "") {
                $solrSearchText = "(" . $solrSearchText . ")";
            }
                // create query string, add query conditions
            if ($search != '') {
                // format search string
                // return exception in case of invalid text
                $search = $this->getSearchText( $search );

                if ($solrSearchText != "" && $search != "") {
                    $solrSearchText .= " AND ";
                }
                if ($search != "") {
                    $solrSearchText .= "(" . $search . ")";
                }
            }
            // add del_index dynamic fields to list of resulting columns
            // the fields begin in the 30th column and more
            $columsToInclude = array_merge( $columsToInclude, $delegationIndexes );

            // if is a counter no records are returned
            if ($doCount) {
                $start = 0;
                $limit = 0;
                $numSortingCols = 0;
                $columsToInclude = array ();
            }

            $data = array ('workspace' => $this->_solrInstance,            // solr instance
                'startAfter' => intval( $start ),'pageSize' => intval( $limit ),'searchText' => $solrSearchText,'filterText' => '',            // $filter, //ex:'field1:value1,field2:[value2.1
            // TO value2.2],field3:value3'
            'numSortingCols' => $numSortingCols,'sortableCols' => $sortableCols,'sortCols' => $sortCols,'sortDir' => $sortDir,'includeCols' => $columsToInclude,'resultFormat' => 'json'
            );

            $solrRequestData = Entity_SolrRequestData::createForRequestPagination( $data );
            // use search index to return list of cases
            $searchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );
            // execute query
            $solrQueryResult = $searchIndex->getDataTablePaginatedList( $solrRequestData );

            if ($this->debug) {
                $this->afterSolrQueryTime = microtime( true );
            }
            //return inmediatelly
            if ($doCount) {
                $result['totalCount'] = $solrQueryResult->iTotalDisplayRecords;
                $result['data'] = array ();
                $result['success'] = true;
                $result['result'] = true;
                $result['message'] = "";

                return $result;
            }
            // complete return data, complete list of columns in grid
            $resultColumns = array ("APP_CREATE_DATE","APP_CURRENT_USER","APP_DEL_PREVIOUS_USER","APP_FINISH_DATE","APP_NUMBER","APP_OVERDUE_PERCENTAGE","APP_PRO_TITLE","APP_STATUS","APP_TAS_TITLE","APP_THREAD_STATUS","APP_TITLE","APP_UID","APP_UPDATE_DATE","DEL_DELAYED","DEL_DELAY_DURATION","DEL_DELEGATE_DATE","DEL_DURATION","DEL_FINISHED","DEL_FINISH_DATE","DEL_INDEX","DEL_INIT_DATE","DEL_PRIORITY","DEL_QUEUE_DURATION","DEL_STARTED","DEL_TASK_DUE_DATE","DEL_THREAD_STATUS","PREVIOUS_USR_UID","PRO_UID","TAS_UID","USR_UID"
            );

            $rows = array ();
            // number of found records
            $result['totalCount'] = $solrQueryResult->iTotalDisplayRecords;

            //get all the data from database
            $appUids = array ();
            foreach ($solrQueryResult->aaData as $i => $data) {
                $appUids[] = $data[11];
            }

            $aaappsDBData = $this->getListApplicationDelegationData($appUids, $action, $status);

            if ($this->debug) {
                $this->afterDbQueryTime = microtime( true );
            }
            // complete the missing data to display it in the grid.
            $delIndexes = array (); //store all the delegation indexes
            foreach ($solrQueryResult->aaData as $i => $data) {
                //initialize array
                $delIndexes = array ();
                // complete empty values
                $appUID = $data[11];

                //get all the delindexes
                for ($i = 30; $i < count( $data ); $i ++) {
                    if (is_array( $data[$i] )) {
                        foreach ($data[$i] as $delIndex) {
                            $delIndexes[] = $delIndex;
                        }
                    }
                }
                // verify if the delindex is an array
                // if is not check different types of repositories
                // the delegation index must always be defined.
                if (count( $delIndexes ) == 0) {
                    if ($action == "search") {
                        //Get all the indexes

                        //$delIndexes = $this->getApplicationDelegationsIndex ($appUID);
                        $indexes = $this->aaSearchRecords( $aaappsDBData, array ('APP_UID' => $appUID
                        ) );

                        foreach ($indexes as $index) {
                            $delIndexes[] = $aaappsDBData[$index]['DEL_INDEX'];
                        }

                    } else {
                        //error an index must always be defined
                        print "Delegation not defined\n";
                    }
                    /*
                     * elseif ($action == 'unassigned'){ $delIndexes = $this->getApplicationDelegationsIndex ($appUID); }
                     */
                }

                $arrayRowAux = array();

                foreach ($resultColumns as $j => $columnName) {
                    $arrayRowAux[$columnName] = $data[$j];
                }

                //Remove duplicated
                $delIndexes = array_unique($delIndexes);

                //Gets the last DEL_INDEX
                sort($delIndexes);
                $delIndexAux = array_pop($delIndexes);

                $delIndexes = array($delIndexAux);

                //Set register
                foreach ($delIndexes as $delIndex) {
                    $aRow = $arrayRowAux;

                    // convert date from solr format UTC to local time in MySQL format
                    $solrdate = $data[0];
                    $localDate = date( 'Y-m-d H:i:s', strtotime( $solrdate ) );
                    $aRow['APP_CREATE_DATE'] = $localDate;

                    $solrdate = $data[12];
                    $localDate = date( 'Y-m-d H:i:s', strtotime( $solrdate ) );
                    $aRow['APP_UPDATE_DATE'] = $localDate;

                    // get delegation data from DB
                    //filter data from db
                    $indexes = $this->aaSearchRecords( $aaappsDBData, array ('APP_UID' => $appUID,'DEL_INDEX' => $delIndex
                    ) );

                    foreach ($indexes as $index) {
                        $row = $aaappsDBData[$index];
                    }

                    if (! isset( $row )) {
                        $fh = fopen( "SolrAppWhitoutDelIndex.txt", 'a' ) or die( "can't open file to store Solr search time." );
                        fwrite( $fh, sprintf( "Solr AppUid: %s DelIndex: %s not found.\r\n", $appUID, $delIndex ) );
                        fclose( $fh );
                        continue;
                    }
                    //$row = $this->getAppDelegationData ($appUID, $delIndex);


                    $aRow['APP_FINISH_DATE'] = null;
                    $aRow['APP_CURRENT_USER'] = $row['USR_NAME'] . " " . $row['USR_LAST'];
                    $aRow['APP_DEL_PREVIOUS_USER'] = $row['USR_PREV_NAME'] . " " . $row['USR_PREV_LAST'];
                    $aRow['APP_OVERDUE_PERCENTAGE'] = $row['APP_OVERDUE_PERCENTAGE'];
                    $aRow['APP_TAS_TITLE'] = $row['APP_TAS_TITLE'];
                    $aRow['APP_THREAD_STATUS'] = $row['APP_THREAD_STATUS'];
                    $aRow['DEL_DELAYED'] = $row['DEL_DELAYED'];
                    $aRow['DEL_DELAY_DURATION'] = $row['DEL_DELAY_DURATION'];
                    $aRow['DEL_DELEGATE_DATE'] = $row['DEL_DELEGATE_DATE'];
                    $aRow['DEL_DURATION'] = $row['DEL_DURATION'];
                    $aRow['DEL_FINISHED'] = (isset( $row['DEL_FINISH_DATE'] ) && $row['DEL_FINISH_DATE'] != '') ? 1 : 0;
                    $aRow['DEL_FINISH_DATE'] = $row['DEL_FINISH_DATE'];
                    $aRow['DEL_INDEX'] = $row['DEL_INDEX'];
                    $aRow['DEL_INIT_DATE'] = $row['DEL_INIT_DATE'];
                    $aRow['DEL_QUEUE_DURATION'] = $row['DEL_QUEUE_DURATION'];
                    $aRow['DEL_STARTED'] = (isset( $row['DEL_INIT_DATE'] ) && $row['DEL_INIT_DATE'] != '') ? 1 : 0;
                    $aRow['DEL_TASK_DUE_DATE'] = $row['DEL_TASK_DUE_DATE'];
                    $aRow['DEL_THREAD_STATUS'] = $row['DEL_THREAD_STATUS'];
                    $aRow['PREVIOUS_USR_UID'] = $row['PREVIOUS_USR_UID'];
                    $aRow['TAS_UID'] = $row['TAS_UID'];
                    $aRow['USR_UID'] = $userUid;
                    $aRow['DEL_PRIORITY'] = G::LoadTranslation( "ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}" );

                    if (isset($aRow["APP_STATUS"])) {
                        $aRow["APP_STATUS"] = G::LoadTranslation("ID_" . $aRow["APP_STATUS"]);
                    }

                    $rows[] = $aRow;
                }
            }

            $result['data'] = $rows;
            $result['success'] = true;
            $result['result'] = true;
            $result['message'] = "";

            /**
             * ******************************************
             */
            if ($this->debug) {
                $this->afterPrepareResultTime = microtime( true );

                $fh = fopen( "SolrSearchTime.txt", 'a' ) or die( "can't open file to store Solr search time." );
                //fwrite($fh, sprintf("Solr Query time: %s DB Query time: %s Prepare result time: %s \n", gmdate ('H:i:s:u', ($this->afterSolrQueryTime - $this->initTimeAll)), gmdate ('H:i:s:u', ($this->afterDbQueryTime - $this->afterSolrQueryTime)), gmdate ('H:i:s:u', ($this->afterPrepareResultTime - $this->afterDbQueryTime))  ));
                fwrite( $fh, sprintf( "Solr Query time: %s DB Query time: %s Prepare result time: %s Total:%s \r\n", ($this->afterSolrQueryTime - $this->initTimeAll), ($this->afterDbQueryTime - $this->afterSolrQueryTime), ($this->afterPrepareResultTime - $this->afterDbQueryTime), ($this->afterPrepareResultTime - $this->initTimeAll) ) );
                fclose( $fh );
            }
            /**
             * ************************************
             */

            return $result;

        } catch (InvalidIndexSearchTextException $ex) {
            // return empty result with description of error
            $result = array ();
            $result['totalCount'] = 0;
            $result['data'] = array ();
            $result['success'] = true;
            $result['result'] = false;
            $result['message'] = $ex->getMessage();
            return $result;
        }
    }

    /**
     * Get the array of counters of cases
     *
     * @param string $userUid the current logged user uid identifier
     */
    public function getCasesCount ($userUid)
    {
        $casesCount = array ();

        // get number of records in todo list
        $data = $this->getAppGridData( $userUid, 0, 0, 'todo', null, null, null, null, null, null, null, null, null, null, null, true );
        $casesCount['to_do'] = $data['totalCount'];
        // get number of records in participated list
        $data = $this->getAppGridData( $userUid, 0, 0, 'sent', null, null, null, null, null, null, null, null, null, null, null, true );
        $casesCount['sent'] = $data['totalCount'];
        // get number of records in draft list
        $data = $this->getAppGridData( $userUid, 0, 0, 'draft', null, null, null, null, null, null, null, null, null, null, null, true );
        $casesCount['draft'] = $data['totalCount'];
        // get number of records in unassigned list
        $data = $this->getAppGridData( $userUid, 0, 0, 'unassigned', null, null, null, null, null, null, null, null, null, null, null, true );
        $casesCount['selfservice'] = $data['totalCount'];

        return $casesCount;
    }

    /**
     * Get the user groups
     *
     * @param string $usrUID the user identifier
     * @return array of user groups
     */
    public function getUserGroups ($usrUID)
    {
        $gu = new GroupUser();
        $rows = $gu->getAllUserGroups( $usrUID );
        return $rows;
    }

    /**
     * Get the application delegation record from database
     *
     * @param string $arrayAppUid array of Application identifiers
     * @param string $action Action (todo, draft, paused, sent, selfservice, unassigned, search)
     * @param string $appStatus Status of Application
     * @return array of arrays with delegation information.
     */
    public function getListApplicationDelegationData($arrayAppUid, $action, $appStatus)
    {
        $c = new Criteria();

        $c->addSelectColumn( AppDelegationPeer::APP_UID );
        $c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

        $c->addAsColumn( 'USR_NAME', 'u.USR_FIRSTNAME' );
        $c->addAsColumn( 'USR_LAST', 'u.USR_LASTNAME' );

        $c->addAsColumn( 'USR_PREV_NAME', 'uprev.USR_FIRSTNAME' );
        $c->addAsColumn( 'USR_PREV_LAST', 'uprev.USR_LASTNAME' );
        $c->addAsColumn( 'PREVIOUS_USR_UID', 'uprev.USR_UID' );

        $c->addAsColumn( 'APP_TAS_TITLE', 'ctastitle.CON_VALUE' );
        $c->addAsColumn( 'APP_THREAD_STATUS', 'at.APP_THREAD_STATUS' );

        $c->addSelectColumn( AppDelegationPeer::APP_OVERDUE_PERCENTAGE );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELAYED );
        $c->addSelectColumn( AppDelegationPeer::DEL_DELAY_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_QUEUE_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_THREAD_STATUS );
        $c->addSelectColumn( AppDelegationPeer::TAS_UID );

        $c->addAlias("u", "USERS");
        $c->addAlias("uprev", "USERS");
        $c->addAlias("adprev", "APP_DELEGATION");
        $c->addAlias("ctastitle", "CONTENT");
        $c->addAlias("at", "APP_THREAD");

        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::APP_UID, ApplicationPeer::APP_UID);
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::USR_UID, "u.USR_UID");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::APP_UID, "adprev.APP_UID");
        $arrayCondition[] = array(AppDelegationPeer::DEL_PREVIOUS, "adprev.DEL_INDEX");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::TAS_UID, "ctastitle.CON_ID");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array("adprev.USR_UID", "uprev.USR_UID");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(AppDelegationPeer::APP_UID, "at.APP_UID");
        $arrayCondition[] = array(AppDelegationPeer::DEL_THREAD, "at.APP_THREAD_INDEX");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $c->add(AppDelegationPeer::APP_UID, $arrayAppUid, Criteria::IN);
        //$c->add (AppDelegationPeer::DEL_INDEX, $delIndex);

        switch ($action) {
            case "sent":
                if (!empty($appStatus)) {
                    $c->add(ApplicationPeer::APP_STATUS, $appStatus);
                }
                break;
            default:
                $appCacheView = new AppCacheView();

                //Paused
                $sqlAppDelay = $appCacheView->getAppDelaySql(AppDelegationPeer::APP_UID, AppDelegationPeer::DEL_INDEX);

                //Criterions
                $criterionToDo = $c->getNewCriterion(ApplicationPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL)->addAnd(
                                 $c->getNewCriterion(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL))->addAnd(
                                 $c->getNewCriterion("at.APP_THREAD_STATUS", "OPEN"))->addAnd(
                                 $c->getNewCriterion(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN"));

                $criterionDraft = $c->getNewCriterion(ApplicationPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL)->addAnd(
                                  $c->getNewCriterion("at.APP_THREAD_STATUS", "OPEN"))->addAnd(
                                  $c->getNewCriterion(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN"));

                $criterionPaused = $c->getNewCriterion(ApplicationPeer::APP_STATUS, "PAUSED")->addAnd(
                                   $c->getNewCriterion(AppDelegationPeer::APP_UID, AppDelegationPeer::APP_UID . " IN ($sqlAppDelay)", Criteria::CUSTOM));

                $criterionCancelled = $c->getNewCriterion(ApplicationPeer::APP_STATUS, "CANCELLED", CRITERIA::EQUAL)->addAnd(
                                      $c->getNewCriterion(AppDelegationPeer::DEL_THREAD_STATUS, "CLOSED"));

                $criterionCompleted = $c->getNewCriterion(ApplicationPeer::APP_STATUS, "COMPLETED");

                switch ($appStatus) {
                    case "TO_DO":
                        //ToDo
                        $c->add($criterionToDo);
                        break;
                    case "DRAFT":
                        //Draft
                        $c->add($criterionDraft);
                        break;
                    case "PAUSED":
                        //Paused
                        $c->add($criterionPaused);
                        break;
                    case "CANCELLED":
                        //Cancelled
                        $c->add($criterionCancelled);
                        break;
                    case "COMPLETED":
                        //Completed
                        $c->add($criterionCompleted);
                        break;
                    default:
                        //All status
                        $c->add(
                            //ToDo
                            $criterionToDo
                        )->addOr(
                            //Draft
                            $criterionDraft
                        )->addOr(
                            //Paused
                            $criterionPaused
                        )->addOr(
                            //Cancelled
                            $criterionCancelled
                        )->addOr(
                            //Completed
                            $criterionCompleted
                        );
                        break;
                }
                break;
        }

        $c->add( 'ctastitle.CON_CATEGORY', 'TAS_TITLE' );
        $c->add( 'ctastitle.CON_LANG', 'en' );

        $rs = AppDelegationPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        // echo $c->toString();
        $rs->next();
        $row = $rs->getRow();

        $appDataRows = array ();
        while (is_array( $row )) {
            $appDataRows[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }

        return $appDataRows;
    }

    /**
     * Get the application delegation record from database
     *
     * @param string $appUID Application identifier
     * @param string $delIndex delegation index
     * @return array with delegation record.
     */
    public function getAppDelegationData ($appUID, $delIndex)
    {

        $c = new Criteria();

        $c->addSelectColumn( AppDelegationPeer::APP_UID );
        $c->addSelectColumn( AppDelegationPeer::DEL_INDEX );

        $c->addAsColumn( 'USR_NAME', 'u.USR_FIRSTNAME' );
        $c->addAsColumn( 'USR_LAST', 'u.USR_LASTNAME' );

        $c->addAsColumn( 'USR_PREV_NAME', 'uprev.USR_FIRSTNAME' );
        $c->addAsColumn( 'USR_PREV_LAST', 'uprev.USR_LASTNAME' );
        $c->addAsColumn( 'PREVIOUS_USR_UID', 'uprev.USR_UID' );

        $c->addAsColumn( 'APP_TAS_TITLE', 'ctastitle.CON_VALUE' );
        $c->addAsColumn( 'APP_THREAD_STATUS', 'at.APP_THREAD_STATUS' );

        $c->addSelectColumn( AppDelegationPeer::APP_OVERDUE_PERCENTAGE );

        $c->addSelectColumn( AppDelegationPeer::DEL_DELAYED );
        $c->addSelectColumn( AppDelegationPeer::DEL_DELAY_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_FINISH_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_INIT_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_QUEUE_DURATION );
        $c->addSelectColumn( AppDelegationPeer::DEL_TASK_DUE_DATE );
        $c->addSelectColumn( AppDelegationPeer::DEL_THREAD_STATUS );
        $c->addSelectColumn( AppDelegationPeer::TAS_UID );

        $c->addAlias( 'u', 'USERS' );
        $c->addAlias( 'uprev', 'USERS' );
        $c->addAlias( 'adprev', 'APP_DELEGATION' );
        $c->addAlias( 'ctastitle', 'CONTENT' );
        $c->addAlias( 'at', 'APP_THREAD' );

        $aConditions = array ();
        $aConditions[] = array (AppDelegationPeer::USR_UID,'u.USR_UID'
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $aConditions = array ();
        $aConditions[] = array (AppDelegationPeer::APP_UID,'adprev.APP_UID'
        );
        $aConditions[] = array (AppDelegationPeer::DEL_PREVIOUS,'adprev.DEL_INDEX'
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $aConditions = array ();
        $aConditions[] = array (AppDelegationPeer::TAS_UID,'ctastitle.CON_ID'
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $aConditions = array ();
        $aConditions[] = array ('adprev.USR_UID','uprev.USR_UID'
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $aConditions = array ();
        $aConditions[] = array (AppDelegationPeer::APP_UID,'at.APP_UID'
        );
        $aConditions[] = array (AppDelegationPeer::DEL_THREAD,'at.APP_THREAD_INDEX'
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $c->add( AppDelegationPeer::APP_UID, $appUID );
        $c->add( AppDelegationPeer::DEL_INDEX, $delIndex );

        $c->add( 'ctastitle.CON_CATEGORY', 'TAS_TITLE' );
        $c->add( 'ctastitle.CON_LANG', 'en' );

        $rs = AppDelegationPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        // echo $c->toString();
        $rs->next();
        $row = $rs->getRow();

        return $row;
    }

    /**
     * return the correct search text for solr.
     * if a field is included only search in this field.
     *
     * @param string $plainSearchText
     * @return string formated Solr search string.
     */
    public function getSearchText ($plainSearchText)
    {
        $formattedSearchText = "";
        // if an error is found in string null is returned
        $includeToken = true;

        // prepare string to separate and join parentesis
                    // " " => " "
        $count = 1;
        while ($count > 0) {
            $plainSearchText = preg_replace( '/\s\s+/', ' ', $plainSearchText, - 1, $count );
        }
        // "text0( text1" => "text0 (text1"; "text0 )text1" => "text0) text1";
        $plainSearchText = preg_replace( '/\s\[\s/', '[', $plainSearchText );
        $plainSearchText = preg_replace( '/\s\]\s/', '] ', $plainSearchText );
        $plainSearchText = preg_replace( '/\s"\s/', '" ', $plainSearchText );

        // print "format search string: " . $plainSearchText . "\n";
                    // format
                    // 1: plain text that is used to search in text field: concat field
                    // 2: a field is specified [field_name]:["phrase search"]
                    // [field_name]:["phrase search"] [field_name]:[word_search] word_search
                    // "phrase search"
                    // to scape a reserved character use a double value: "::", """"
                    // ex: (APP_ASSIGNED_USERS:7091676694d9269da75c254003021135) AND
                    // (contrato_t:76* AND Causal_t:1021 AND Materiales AND 143073)
                    // ex: date search => APP_CREATE_DATE:[2012-03-12T00:00:00Z TO
                    // 2012-04-12T00:00:00Z]
                    // ex: phrase => TEXT:"This is a lazy dog"


        // search the first


        // cache the index fields
        G::LoadClass( 'PMmemcached' );
        $oMemcache = PMmemcached::getSingleton( $this->_solrInstance );
        $ListFieldsInfo = $oMemcache->get( 'Solr_Index_Fields' );
        if (! $ListFieldsInfo) {
            G::LoadClass( 'searchIndex' );

            $searchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );
            // execute query
            $ListFieldsInfo = $searchIndex->getIndexFields( $this->_solrInstance );

            //var_dump($ListFieldsInfo);
                    // cache
            $oMemcache->set( 'Solr_Index_Fields', $ListFieldsInfo );

        }

        $tok = strtok( $plainSearchText, " " );

        while ($tok !== false) {
            $fieldName = substr( $tok, 0, strpos( $tok, ":" ) ); // strstr ( $tok,
                    // ":",
                    // true ); php 5.3
            $searchText = strstr( $tok, ":" );

            // verify if there's a field definition
            if ($fieldName === false || $fieldName == "") {
                // it's not a field
                    // the token is not a field
                    // add it completelly
                $includeToken = true;
                // no field found
                $formattedSearchText .= $tok; // used to search in the general default
                        // text field
            } else {
                // it's a field
                    // verify if is complete
                if ($fieldName == "" || $searchText == ":") {
                    $includeToken = false;
                    throw new InvalidIndexSearchTextException( "Invalid search text, verify the syntax. Expected format = {variable_name}:{search_text}" );
                }

                // field name found
                    // search index field name
                $indexFieldName = "";
                if (array_key_exists( $fieldName, $ListFieldsInfo )) {
                    $indexFieldName = $ListFieldsInfo[$fieldName];
                } else {
                    // no field name found
                    // don't include field search
                    // return message about it
                    $includeToken = false;
                    throw new InvalidIndexSearchTextException( "Invalid search text, variable not found." );
                }

                // The token is part of a phrase, date or a word?
                if ($searchText[1] == "[" || $searchText[1] == "\"") {
                    // expecting date interval we must search the end of the phrase
                    // the phrase is complete?
                    if ($searchText[1] == "[" && $searchText[strlen( $searchText ) - 1] == "]") {
                        // complete phrase ok, the date must be validated
                        // throw new InvalidIndexSearchTextException("Invalid search text.
                        // Expected date interval format =>
                        // {variable_name}:[YYYY-MM-DDThh:mm:ssZ TO YYYY-MM-DDThh:mm:ssZ]");
                    } elseif ($searchText[1] == "\"" && $searchText[strlen( $searchText ) - 1] == "\"") {
                        // the phrase is complete and is ok.
                    } else {
                        // search end of phrase
                        $tok = strtok( " " );
                        $found = false;
                        while ($tok !== false) {
                            if ((($searchText[1] == "[") && ($tok[strlen( $tok ) - 1] == "]")) || (($searchText[1] == "\"") && ($tok[strlen( $tok ) - 1] == "\""))) {
                                // end of phrase found
                                $found = true;
                                $searchText .= " " . $tok;
                                break;
                            } else {
                                // continue adding text
                                $searchText .= " " . $tok;
                            }
                            $tok = strtok( " " );
                        }
                        if (! $found) {
                            // error invalid text
                            // Expected date interval format => {variable_name}:[YYYY-MM-DDThh:mm:ssZ TO YYYY-MM-DDThh:mm:ssZ]
                            throw new InvalidIndexSearchTextException( "Invalid search text. The date or phase is not completed" );
                        }
                    }
                }

                // validate phrase in case of date
                if (($searchText[1] == "[")) {
                    // validate date range format
                    // use regular expresion to validate it [yyyy-mm-dd TO yyyy-mm-dd]
                    $reg = "/:\[(\d\d\d\d-\d\d-\d\d|\*)\sTO\s(\d\d\d\d-\d\d-\d\d|\*)\]/";
                    // convert date to utc
                    $matched = preg_match( $reg, $searchText, $matches );
                    if ($matched == 1) {
                        // the date interval is valid
                        // convert to SOlr format
                        $fromDateOriginal = $matches[1];
                        $fromDate = $matches[1];

                        $toDateOriginal = $matches[2];
                        $toDate = $matches[2];

                        if ($fromDateOriginal != '*') {
                            // TODO complete date creation
                            // list($year, $month, $day) = sscanf($fromDateOriginal,
                            // '%04d/%02d/%02d');
                            // $fromDateDatetime = new DateTime($fromDateOriginal);
                            // $fromDateDatetime = date_create_from_format ( 'Y-m-d',
                            // $fromDateOriginal );
                            // $fromDateDatetime->getTimestamp ()
                            $fromDate = gmdate( "Y-m-d\T00:00:00\Z", strtotime( $fromDateOriginal ) );
                        }
                        if ($toDateOriginal != '*') {
                            // list($year, $month, $day) = sscanf($fromDateOriginal,
                            // '%04d/%02d/%02d');
                            // $toDateDatetime = new DateTime($toDateOriginal);
                            // $toDateDatetime = date_create_from_format ( 'Y-m-d',
                            // $toDateOriginal );
                            $toDate = gmdate( "Y-m-d\T23:59:59.999\Z", strtotime( $toDateOriginal ) );
                        }
                        $searchText = ":[" . $fromDate . " TO " . $toDate . "]";
                    } else {
                        throw new InvalidIndexSearchTextException( "Invalid search text. Expected date interval format => {variable_name}:[YYYY-MM-DD TO YYYY-MM-DD]" );
                    }
                }

                $formattedSearchText .= $indexFieldName . $searchText;
                $includeToken = true;
            }

            if ($includeToken) {
                $formattedSearchText .= " AND ";
            }
            // next token
            $tok = strtok( " " );
        }
        // remove last AND
        $formattedSearchText = substr_replace( $formattedSearchText, "", - 5 );
        return $formattedSearchText;
    }

    /**
     * Get all the application delegation records from database
     *
     * @param string $appUID Application identifier
     * @return array delegation records
     */
    public function getApplicationDelegationsIndex ($appUID)
    {
        $delIndexes = array ();

        $c = new Criteria();

        $c->addSelectColumn( AppDelegationPeer::DEL_INDEX );
        $c->add( AppDelegationPeer::APP_UID, $appUID );

        $rs = AppDelegationPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rs->next();
        $row = $rs->getRow();

        while (is_array( $row )) {
            $delIndexes[] = $row['DEL_INDEX'];
            $rs->next();
            $row = $rs->getRow();
        }

        return $delIndexes;

    }

    /**
     * Update the information of the specified applications in Solr
     *
     * @param array $aaAPPUIDs Array of arrays of App_UID that must be updated,
     *        APP_UID is permitted also
     */
    public function updateApplicationSearchIndex ($aaAPPUIDs)
    {
        if (empty( $aaAPPUIDs )) {
            return;
        }

        if (! is_array( $aaAPPUIDs )) {
            // convert to array
            $APPUID = $aaAPPUIDs;
            $aaAPPUIDs = array ();
            $aaAPPUIDs[] = array ('APP_UID' => $APPUID
            );
        }
        // check if index server is available
        if (! $this->isSolrEnabled()) {
            // store update in table and return
            foreach ($aaAPPUIDs as $aAPPUID) {
                $this->applicationChangedUpdateSolrQueue( $aAPPUID['APP_UID'], true );
            }
            return;
        }

        if ($this->debug) {
            $this->getApplicationDataDBTime = 0;
            $this->getPreparedApplicationDataDBTime = 0;
            $this->getBuilXMLDocTime = 0;
       
            $this->beforeCreateSolrXMLDocTime = microtime( true );
        }
        // create XML document
        $xmlDoc = $this->createSolrXMLDocument( $aaAPPUIDs );

        if ($this->debug) {
            $this->afterCreateSolrXMLDocTime = microtime( true );
        }
        // update document
        $data = array ('workspace' => $this->_solrInstance,'document' => $xmlDoc
        );

        $oSolrUpdateDocument = Entity_SolrUpdateDocument::createForRequest( $data );

        G::LoadClass( 'searchIndex' );

        $oSearchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );

        try {
            $oSearchIndex->updateIndexDocument( $oSolrUpdateDocument );

            if ($this->debug) {
                $this->afterUpdateSolrXMLDocTime = microtime( true );
            }
            // commit changes
            //$oSearchIndex->commitIndexChanges( $this->_solrInstance );

        } catch (Exception $ex) {
            //print "Excepcion indexing data: " . $ex->getMessage() . "\n"; die;
            $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
            fwrite( $fh, $ex->getMessage() );
            fclose( $fh );
        }
        if ($this->debug) {

            $fh = fopen("SolrIndexTime.txt", 'a') or die("can't open file to store Solr index time.");
            //fwrite($fh, sprintf("Solr Query time: %s DB Query time: %s Prepare result time: %s \n", gmdate ('H:i:s:u', ($this->afterSolrQueryTime - $this->initTimeAll)), gmdate ('H:i:s:u', ($this->afterDbQueryTime - $this->afterSolrQueryTime)), gmdate ('H:i:s:u', ($this->afterPrepareResultTime - $this->afterDbQueryTime))  ));
            $trunkSize = count($aaAPPUIDs);
            $this->trunkSizeAcumulated += $trunkSize;
            $this->totalTimeAcumulated += ($this->afterUpdateSolrXMLDocTime - $this->beforeCreateSolrXMLDocTime);

            //Solr App trunk size| Get Data from DB (s)| Prepare DB data (s) | Create XML file (s)| Create XML Document total (s)| Update Solr Document (s)
            fwrite($fh, sprintf("%s|%s|%s|%s|%s|%s|%s|%s\r\n", 
              $this->trunkSizeAcumulated,
              $this->totalTimeAcumulated,
              $this->getApplicationDataDBTime, 
              $this->getPreparedApplicationDataDBTime, 
              $this->getBuilXMLDocTime,
              ($this->afterCreateSolrXMLDocTime - $this->beforeCreateSolrXMLDocTime), 
              ($this->afterUpdateSolrXMLDocTime - $this->afterCreateSolrXMLDocTime),
              ($this->afterUpdateSolrXMLDocTime - $this->beforeCreateSolrXMLDocTime)
              ));

            fclose($fh);

            /*
            $this->afterCommitSolrDocTime = microtime( true );

            $fh = fopen( "SolrIndexTime.txt", 'a' ) or die( "can't open file to store Solr index time." );
            //fwrite($fh, sprintf("Solr Query time: %s DB Query time: %s Prepare result time: %s \n", gmdate ('H:i:s:u', ($this->afterSolrQueryTime - $this->initTimeAll)), gmdate ('H:i:s:u', ($this->afterDbQueryTime - $this->afterSolrQueryTime)), gmdate ('H:i:s:u', ($this->afterPrepareResultTime - $this->afterDbQueryTime))  ));
            fwrite( $fh, sprintf( "Solr Create XML Document time: %s Update Solr Document time: %s Commit Solr Changes time: %s Total:%s \r\n", ($this->afterCreateSolrXMLDocTime - $this->beforeCreateSolrXMLDocTime), ($this->afterUpdateSolrXMLDocTime - $this->afterCreateSolrXMLDocTime), ($this->afterCommitSolrDocTime - $this->afterUpdateSolrXMLDocTime), ($this->afterCommitSolrDocTime - $this->beforeCreateSolrXMLDocTime) ) );
            fclose( $fh );*/
        }
    }

    /**
     * Delete the specified application record from Solr
     *
     * @param string $appUID Application identifier
     */
    public function deleteApplicationSearchIndex ($appUID)
    {
        if (empty( $appUID )) {
            return;
        }

        // check if index server is available
        if (! $this->_solrIsEnabled) {
            // store update in table and return
            $this->applicationChangedUpdateSolrQueue( $appUID['APP_UID'], 2 ); // delete
            return;
        }

        $idQuery = "APP_UID:" . $appUID;

        G::LoadClass( 'searchIndex' );

        $oSearchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );

        $oSearchIndex->deleteDocumentFromIndex( $this->_solrInstance, $idQuery );

        // commit changes
        $oSearchIndex->commitIndexChanges( $this->_solrInstance );
    }

    /**
     * Create XML data in Solr format of the specified applications
     * this function uses the buildSearchIndexDocumentPMOS2 function to create
     * each record
     *
     * @param array $aaAPPUIDs array of arrays of application identifiers
     * @return string The resulting XML document in Solr format
     */
    public function createSolrXMLDocument ($aaAPPUIDs)
    {
        if ($this->debug) {
            $this->getApplicationDataDBTime = 0;
            $this->getPreparedApplicationDataDBTime = 0;
            $this->getBuilXMLDocTime = 0;            
        }
        // search data from DB
        $xmlDoc = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xmlDoc .= "<add commitWithin='5000'>\n";

        //get all application data from DB of all applications and delegations
        $aAPPUIDs = array ();
        foreach ($aaAPPUIDs as $aAPPUID) {
            $aAPPUIDs[] = $aAPPUID['APP_UID'];
        }
        if ($this->debug) {
            $this->beforeGetApplicationDataDBTime = microtime( true );
        }
        $aaAllAppDelData = $this->getListApplicationUpdateDelegationData( $aAPPUIDs );
        if ($this->debug) {
            $this->afterGetApplicationDataDBTime = microtime (true);
          
            $this->getApplicationDataDBTime = $this->afterGetApplicationDataDBTime - $this->beforeGetApplicationDataDBTime;
        }
        foreach ($aaAPPUIDs as $aAPPUID) {
            try {

                if ($this->debug) {
                    $this->beforePrepareApplicationDataDBTime = microtime( true );
                }
                //filter data, include all the rows of the application
                    // get delegation data from DB
                $aaAppData = array ();
                //filter data from db
                $indexes = $this->aaSearchRecords($aaAllAppDelData, array("APP_UID" => $aAPPUID["APP_UID"]));

                foreach ($indexes as $index) {
                    $aaAppData[] = $aaAllAppDelData[$index];
                }

                $result = $this->getApplicationIndexData( $aAPPUID['APP_UID'], $aaAppData );

                if ($this->debug) {
                
                  $this->afterPrepareApplicationDataDBTime = microtime (true);
                
                  $this->getPreparedApplicationDataDBTime += $this->afterPrepareApplicationDataDBTime - $this->beforePrepareApplicationDataDBTime;                    
                }

            } catch (ApplicationWithoutDelegationRecordsException $ex) {
                // exception trying to get application information
                    // skip and continue with the next application
                $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
                fwrite( $fh, $ex->getMessage() );
                fclose( $fh );
                continue;
            } catch (ApplicationWithCorruptDynaformException $ex) {
                $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
                fwrite( $fh, $ex->getMessage() );
                fclose( $fh );
                continue;
            } catch (Exception $ex) {
                $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
                fwrite( $fh, "getApplicationIndexData " . $aAPPUID['APP_UID'] . ":" . $ex->getMessage() . "\n" );
                fclose( $fh );
                continue;
            }

            $documentInformation = $result[0];
            $dynaformFieldTypes = $result[1];
            $lastUpdateDate = $result[2];
            $maxPriority = $result[3];
            $assignedUsers = $result[4];
            $assignedUsersRead = $result[5];
            $assignedUsersUnread = $result[6];
            $draftUser = $result[7];
            $participatedUsers = $result[8];
            $participatedUsersStartedByUser = $result[9];
            $participatedUsersCompletedByUser = $result[10];
            $unassignedUsers = $result[11];
            $unassignedGroups = $result[12];
            $pausedUser = $result[13];
            $cancelledUser = $result[14];

            try {

                // create document
                $xmlDoc = $xmlDoc . $this->buildSearchIndexDocumentPMOS2(
                    $documentInformation,
                    $dynaformFieldTypes,
                    $lastUpdateDate,
                    $maxPriority,
                    $assignedUsers,
                    $assignedUsersRead,
                    $assignedUsersUnread,
                    $draftUser,
                    $participatedUsers,
                    $participatedUsersStartedByUser,
                    $participatedUsersCompletedByUser,
                    $unassignedUsers,
                    $unassignedGroups,
                    $pausedUser,
                    $cancelledUser
                );

                if ($this->debug) {
                    $this->afterBuilXMLDocTime = microtime (true);
                    
                    $this->getBuilXMLDocTime += $this->afterBuilXMLDocTime - $this->afterPrepareApplicationDataDBTime;                   
                }
            } catch (ApplicationAPP_DATAUnserializeException $ex) {
                // exception trying to get application information
                $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
                fwrite( $fh, $ex->getMessage() );
                fclose( $fh );
                // skip and continue with the next application
                continue;
            } catch (Exception $ex) {
                $fh = fopen( "./SolrIndexErrors.txt", 'a' ) or die( "can't open file to store Solr index errors." );
                fwrite( $fh, "buildSearchIndexDocumentPMOS2 " . $aAPPUID['APP_UID'] . ":" . $ex->getMessage() . "\n" );
                fclose( $fh );
                continue;
            }

          if($this->debugAppInfo)
          {
            $fh = fopen("SolrAPPUIDIndexSize.txt", 'a') or die("can't open file to store Solr index time.");
            //fwrite($fh, sprintf("APP UID %s => doc size: %s\r\n", 
            //  $aAPPUID['APP_UID'], strlen($xmlCurrentDoc)));
            fwrite($fh, sprintf("%s|%s|%s\r\n", 
              $documentInformation ['APP_NUMBER'], $aAPPUID['APP_UID'], strlen($xmlCurrentDoc)));
            fclose($fh);
          }            

        }//end foreach

        $xmlDoc .= "</add>\n";

        /*
        if ($this->debug) {
            $fh = fopen( "SolrIndexTime.txt", 'a' ) or die( "can't open file to store Solr index time." );
            fwrite( $fh, sprintf( "Get Data DB time: %s Prepare DB data Time: %s Create XML file time: %s \r\n", $this->getApplicationDataDBTime, $this->getPreparedApplicationDataDBTime, $this->getBuilXMLDocTime ) );
            fclose( $fh );
        }*/

        return $xmlDoc;
    }

    /**
     * build Solr index document xml for an application
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @param [in] array $documentData array of data for the xml document of
     *        application
     * @param [in] array $dynaformFieldTypes array of dynaform field types, used
     *        to store the info of APP_DATA with types
     * @param [in] array $appTitles array of array of application titles in all
     *        languages
     * @param [in] array $proTitles array of array of process titles in all
     *        languages
     * @param [in] array $assignedUsers array of array of uids of assigned users
     *        to Application UIDs
     * @param [in] array $draftUsers array of array of uids of draft users to
     *        Application UIDs
     * @param [in] array $participatedUsers array of array of participated users
     *        UIDs in application
     * @param [in] array $unassignedUsers array of unassigned users UIDs
     * @param [in] array $unassignedGroups array of unassigned groups UIDs
     * @param [in] array $pausedUser array of array of uids of paused users to Application UIDs
     * @param [in] array $cancelledUser array of array of uids of cancelled users to Application UIDs
     * @param [out] xml xml document
     *
     *        $xmlDoc .= buildSearchIndexDocumentPMOS2($documentInformation,
     *        $dynaformFieldTypes,
     *        $lastUpdateDate, $maxPriority,
     *        $assignedUsers, $assignedUsersRead, $assignedUsersUnread,
     *        $draftUser,
     *        $participatedUsers, $participatedUsersStartedByUser,
     *        $participatedUsersCompletedByUser,
     *        $unassignedUsers, $unassignedGroups, $pausedUser, $cancelledUser);
     */
    public function buildSearchIndexDocumentPMOS2(
        $documentData,
        $dynaformFieldTypes,
        $lastUpdateDate,
        $maxPriority,
        $assignedUsers,
        $assignedUsersRead,
        $assignedUsersUnread,
        $draftUser,
        $participatedUsers,
        $participatedUsersStartedByUser,
        $participatedUsersCompletedByUser,
        $unassignedUsers,
        $unassignedGroups,
        $pausedUser,
        $cancelledUser
    ) {
        // build xml document


        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent( 4 );

        $writer->startElement( "doc" );

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'APP_UID' );
        $writer->text( $documentData['APP_UID'] );
        $writer->endElement();

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'APP_NUMBER' );
        $writer->text( $documentData['APP_NUMBER'] );
        $writer->endElement();

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'APP_STATUS' );
        $writer->text( $documentData['APP_STATUS'] );
        $writer->endElement();

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'PRO_UID' );
        $writer->text( $documentData['PRO_UID'] );
        $writer->endElement();

        if (! empty( $documentData['APP_TITLE'] )) {
            $writer->startElement( "field" );
            $writer->writeAttribute( 'name', 'APP_TITLE' );
            $writer->text( $documentData['APP_TITLE'] );
            $writer->endElement();
        } else {
            $writer->startElement( "field" );
            $writer->writeAttribute( 'name', 'APP_TITLE' );
            $writer->text( "" );
            $writer->endElement();
        }

        if (! empty( $documentData['PRO_TITLE'] )) {
            $writer->startElement( "field" );
            $writer->writeAttribute( 'name', 'APP_PRO_TITLE' );
            $writer->text( $documentData['PRO_TITLE'] );
            $writer->endElement();

        } else {
            $writer->startElement( "field" );
            $writer->writeAttribute( 'name', 'APP_PRO_TITLE' );
            $writer->text( "" );
            $writer->endElement();
        }

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'APP_CREATE_DATE' );
        // convert date to UTC with gmdate
        $writer->text( gmdate( "Y-m-d\TH:i:s\Z", strtotime( $documentData['APP_CREATE_DATE'] ) ) );
        $writer->endElement();

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'DEL_LAST_UPDATE_DATE' );
        // convert date to UTC with gmdate
        $writer->text( gmdate( "Y-m-d\TH:i:s\Z", strtotime( $lastUpdateDate ) ) );
        $writer->endElement();

        $writer->startElement( "field" );
        $writer->writeAttribute( 'name', 'DEL_MAX_PRIORITY' );
        $writer->text( $maxPriority );
        $writer->endElement();

        if (!empty($documentData["PRO_CATEGORY_UID"])) {
            $writer->startElement("field");
            $writer->writeAttribute("name", "PRO_CATEGORY_UID_s");
            $writer->text($documentData["PRO_CATEGORY_UID"]);
            $writer->endElement();
        }

        if (is_array( $assignedUsers ) && ! empty( $assignedUsers )) {
            foreach ($assignedUsers as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USERS' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USER_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();

            }
        }

        if (is_array( $assignedUsersRead ) && ! empty( $assignedUsersRead )) {
            foreach ($assignedUsersRead as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USERS_READ' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USER_READ_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (is_array( $assignedUsersUnread ) && ! empty( $assignedUsersUnread )) {
            foreach ($assignedUsersUnread as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USERS_UNREAD' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_ASSIGNED_USER_UNREAD_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (count($draftUser) > 0) {
            $writer->startElement("field");
            $writer->writeAttribute("name", "APP_DRAFT_USER");
            $writer->text($draftUser["USR_UID"]);
            $writer->endElement();

            //Add field for DEL_INDEX information
            $writer->startElement("field");
            $writer->writeAttribute("name", "APP_DRAFT_USER_DEL_INDEX_" . trim($draftUser["USR_UID"]) . "_txt");
            $writer->text($draftUser["DEL_INDEX"]);
            $writer->endElement();
        }

        if (count($pausedUser) > 0) {
            $writer->startElement("field");
            $writer->writeAttribute("name", "APP_PAUSED_USERS");
            $writer->text($pausedUser["USR_UID"]);
            $writer->endElement();

            //Add field for DEL_INDEX information
            $writer->startElement("field");
            $writer->writeAttribute("name", "APP_PAUSED_USERS_DEL_INDEX_" . trim($pausedUser["USR_UID"]) . "_txt");
            $writer->text($pausedUser["DEL_INDEX"]);
            $writer->endElement();
        }

        if (count($cancelledUser) > 0) {
            foreach ($cancelledUser as $userUid) {
                $writer->startElement("field");
                $writer->writeAttribute("name", "APP_CANCELLED_USER_txt");
                $writer->text($userUid["USR_UID"]);
                $writer->endElement();

                //Add field for DEL_INDEX information
                $writer->startElement("field");
                $writer->writeAttribute("name", "APP_CANCELLED_USER_DEL_INDEX_" . trim($userUid["USR_UID"]) . "_txt");
                $writer->text($userUid["DEL_INDEX"]);
                $writer->endElement();
            }
        }

        if (is_array( $participatedUsers ) && ! empty( $participatedUsers )) {
            foreach ($participatedUsers as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USERS' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USER_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (is_array( $participatedUsersStartedByUser ) && ! empty( $participatedUsersStartedByUser )) {
            foreach ($participatedUsersStartedByUser as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USERS_STARTED' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USER_STARTED_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (is_array( $participatedUsersCompletedByUser ) && ! empty( $participatedUsersCompletedByUser )) {
            foreach ($participatedUsersCompletedByUser as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USERS_COMPLETED' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_PARTICIPATED_USER_COMPLETED_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (is_array( $unassignedUsers ) && ! empty( $unassignedUsers )) {
            foreach ($unassignedUsers as $userUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_UNASSIGNED_USERS' );
                $writer->text( $userUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_UNASSIGNED_USER_GROUP_DEL_INDEX_' . trim( $userUID['USR_UID'] ) . '_txt' );
                $writer->text( $userUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        if (is_array( $unassignedGroups ) && ! empty( $unassignedGroups )) {
            foreach ($unassignedGroups as $groupUID) {
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_UNASSIGNED_GROUPS' );
                $writer->text( $groupUID['USR_UID'] );
                $writer->endElement();

                // add dynamic field for del_index information
                $writer->startElement( "field" );
                $writer->writeAttribute( 'name', 'APP_UNASSIGNED_USER_GROUP_DEL_INDEX_' . trim( $groupUID['USR_UID'] ) . '_txt' );
                $writer->text( $groupUID['DEL_INDEX'] );
                $writer->endElement();
            }
        }

        // get the serialized fields
        if (! empty( $documentData['APP_DATA'] ) && $documentData['APP_DATA'] != "N;") {

            $UnSerializedCaseData = unserialize( $documentData['APP_DATA'] );

            if ($UnSerializedCaseData === false) {
                $UnSerializedCaseData = preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $documentData['APP_DATA'] ); // utf8_encode
                $UnSerializedCaseData = unserialize( $UnSerializedCaseData );
            }

            if (! $UnSerializedCaseData) {
                // error unserializing
                throw new ApplicationAPP_DATAUnserializeException( "Could not unserialize APP_DATA of APP_UID: " . $documentData['APP_UID'] . "\n" );
            } else {
                foreach ($UnSerializedCaseData as $k => $value) {
                    if (! is_array( $value ) && ! is_object( $value ) && $value != '' && $k != 'SYS_LANG' && $k != 'SYS_SKIN' && $k != 'SYS_SYS') {
                        // search the field type in array of dynaform fields
                        if (! empty( $dynaformFieldTypes ) && array_key_exists( trim( $k ), $dynaformFieldTypes )) {
                            $type = $dynaformFieldTypes[trim( $k )];
                            $typeSufix = '_t';
                            switch ($type) {
                                case 'text':
                                    $typeSufix = '_t';
                                    break;
                                case 'Int':
                                    if (intval( $value ) > 2147483647) {
                                        $typeSufix = '_tl'; //for long values
                                        $value = intval( $value );
                                    } else {
                                        $typeSufix = '_ti';
                                        $value = intval( $value );
                                    }
                                    break;
                                case 'Real':
                                    $typeSufix = '_td';
                                    $value = floatval( $value );
                                    break;
                                case 'date':
                                    $newdate = false;
                                    $withHour = true;
                                    // try to convert string to date
                                    // TODO convert to php 5.2 format
                                    /*
                                     * $newdate = date_create_from_format ( 'Y-m-d H:i:s', $value ); if (! $newdate) { $newdate = date_create_from_format ( 'Y-m-d', $value ); $withHour = false; } if (! $newdate) { $newdate = date_create_from_format ( 'd/m/Y', $value ); $withHour = false; } if (!
                                     * $newdate) { $newdate = date_create_from_format ( 'j/m/Y', $value ); $withHour = false; }
                                     */
                                    $newdate = strtotime( $value );
                                    if (! $newdate) {
                                        $typeSufix = '*'; // not store field
                                    } else {
                                        $typeSufix = '_tdt';
                                        /*
                                         * if ($withHour) //$value = gmdate ( "Y-m-d\TH:i:s\Z", $newdate->getTimestamp () ); $value = gmdate ( "Y-m-d\TH:i:s\Z", $newdate ); else { $value = gmdate ( "Y-m-d\T00:00:00\Z", $newdate ); }
                                         */
                                        $value = gmdate( "Y-m-d\T00:00:00\Z", $newdate );
                                    }
                                    break;
                                case 'dropdown':
                                    $typeSufix = '_t';
                                    break;
                                case 'textarea':
                                    $typeSufix = '_t';
                                    break;
                                case 'currency':
                                    $typeSufix = '_td';
                                    $value = floatval( $value );
                                    break;
                                case 'percentage':
                                    $typeSufix = '_t';
                                    break;
                                case 'password':
                                    $typeSufix = '_t';
                                    break;
                                case 'suggest':
                                    $typeSufix = '_t';
                                    break;
                                case 'yesno':
                                    $typeSufix = '_t';
                                    break;
                                case 'listbox':
                                    $typeSufix = '_t';
                                    break;
                                case 'checkbox':
                                    $typeSufix = '_t';
                                    break;
                                case 'checkgroup':
                                    $typeSufix = '_t';
                                    break;
                                case 'radiogroup':
                                    $typeSufix = '_t';
                                    break;
                                case 'hidden':
                                    $typeSufix = '_t';
                                    break;
                            }
                            if ($typeSufix != '*') {
                                $writer->startElement( "field" );
                                $writer->writeAttribute( 'name', trim( $k ) . $typeSufix );
                                $writer->text( $value );
                                $writer->endElement();
                            }
                        } else {
                            $writer->startElement( "field" );
                            $writer->writeAttribute( 'name', trim( $k ) . '_t' );
                            $writer->text( $value );
                            $writer->endElement();
                        }
                    }
                } // foreach unserialized data
            } // else unserialize APP_DATA
        } // empty APP_DATA


        $writer->endElement(); // end /doc


        return $writer->outputMemory( true );
    }

    /**
     * Search records in specified application delegation data
     *
     * @param string $AppUID application identifier
     * @param string $allAppDbData array of rows (array) with application data
     * @throws ApplicationWithoutDelegationRecordsException
     * @return array array of arrays with the following information(
     *         $documentInformation,
     *         $dynaformFieldTypes,
     *         $lastUpdateDate,
     *         $maxPriority,
     *         $assignedUsers,
     *         $assignedUsersRead,
     *         $assignedUsersUnread,
     *         $draftUser,
     *         $participatedUsers,
     *         $participatedUsersStartedByUser,
     *         $participatedUsersCompletedByUser,
     *         $unassignedUsers,
     *         $unassignedGroups,
     *         $pausedUser,
     *         $cancelledUser
     */
    public function getApplicationIndexData ($AppUID, $allAppDbData)
    {
        G::LoadClass( 'memcached' );

        // get all the application data
        //$allAppDbData = $this->getApplicationDelegationData ($AppUID);
        // check if the application record was found
        // this case occurs when the application doesn't have related delegation
        // records.
        if (empty( $allAppDbData ) || ! isset( $allAppDbData[0] )) {
            throw new ApplicationWithoutDelegationRecordsException( "Application without delegation records. APP_UID: " . $AppUID . "\n" );
        }

        // copy the application information
        $documentInformation = $allAppDbData[0];

        // get the last delegate date using the del_delegate_date
        $index = $this->aaGetMaximun( $allAppDbData, 'DEL_DELEGATE_DATE', 'DATE' );

        $lastUpdateDate = $allAppDbData[$index]['DEL_DELEGATE_DATE'];

        // get the delegate with max priority => minimun value
        $index2 = $this->aaGetMinimun( $allAppDbData, 'DEL_PRIORITY', 'NUMBER', 'DEL_THREAD_STATUS', 'OPEN' );

        if ($index2 == null) {
            // get the last priority
            $maxPriority = $allAppDbData[$index]['DEL_PRIORITY'];
        } else {
            $maxPriority = $allAppDbData[$index2]['DEL_PRIORITY'];
        }

        $assignedUsers = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('DEL_THREAD_STATUS' => 'OPEN','DEL_FINISH_DATE' => 'NULL','APP_STATUS' => 'TO_DO','APP_THREAD_STATUS' => 'OPEN'
        ) );
        foreach ($indexes as $index) {
            $assignedUsers[] = array ('USR_UID' => $allAppDbData[$index]['USR_UID'],'DEL_INDEX' => $allAppDbData[$index]['DEL_INDEX']
            );
        }

        $assignedUsersRead = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('DEL_THREAD_STATUS' => 'OPEN','DEL_FINISH_DATE' => 'NULL','APP_STATUS' => 'TO_DO','APP_THREAD_STATUS' => 'OPEN','DEL_INIT_DATE' => 'NOTNULL'
        ) );
        foreach ($indexes as $index) {
            $assignedUsersRead[] = array ('USR_UID' => $allAppDbData[$index]['USR_UID'],'DEL_INDEX' => $allAppDbData[$index]['DEL_INDEX']
            );
        }

        $assignedUsersUnread = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('DEL_THREAD_STATUS' => 'OPEN','DEL_FINISH_DATE' => 'NULL','APP_STATUS' => 'TO_DO','APP_THREAD_STATUS' => 'OPEN','DEL_INIT_DATE' => 'NULL'
        ) );
        foreach ($indexes as $index) {
            $assignedUsersUnread[] = array ('USR_UID' => $allAppDbData[$index]['USR_UID'],'DEL_INDEX' => $allAppDbData[$index]['DEL_INDEX']
            );
        }

        $draftUser = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('DEL_THREAD_STATUS' => 'OPEN','DEL_FINISH_DATE' => 'NULL','APP_STATUS' => 'DRAFT','APP_THREAD_STATUS' => 'OPEN'
        ) );
        if (! empty( $indexes )) {
            $draftUser = array ('USR_UID' => $allAppDbData[$indexes[0]]['USR_UID'],'DEL_INDEX' => $allAppDbData[$indexes[0]]['DEL_INDEX']
            );
        }

        //Paused
        $pausedUser = array();

        foreach ($allAppDbData as $row) {
            if ((empty($row["APPDELY_APP_DISABLE_ACTION_USER"]) || $row["APPDELY_APP_DISABLE_ACTION_USER"] == "0") &&
                !empty($row["APPDELY_APP_DELAY_UID"]) && $row["APPDELY_APP_TYPE"] == "PAUSE"
            ) {
                $pausedUser = array("USR_UID" => $row["USR_UID"], "DEL_INDEX" => $row["DEL_INDEX"]);
                break;
            }
        }

        //Cancelled
        $cancelledUser = array();

        $arrayIndex = $this->aaSearchRecords(
            $allAppDbData,
            array("APP_STATUS" => "CANCELLED", "DEL_THREAD_STATUS" => "CLOSED")
        );

        foreach ($arrayIndex as $index) {
            $cancelledUser[] = array(
                "USR_UID" => $allAppDbData[$index]["USR_UID"],
                "DEL_INDEX" => $allAppDbData[$index]["DEL_INDEX"]
            );
        }

        //Participated
        $participatedUsers = array ();
        foreach ($allAppDbData as $row) {
            $participatedUsers[] = array ('USR_UID' => $row['USR_UID'],'DEL_INDEX' => $row['DEL_INDEX']
            );
        }

        $participatedUsersStartedByUser = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('DEL_INDEX' => '1'
        ) );
        foreach ($indexes as $index) {
            $participatedUsersStartedByUser[] = array ('USR_UID' => $allAppDbData[$index]['USR_UID'],'DEL_INDEX' => $allAppDbData[$index]['DEL_INDEX']
            );
        }

        $participatedUsersCompletedByUser = array ();
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('APP_STATUS' => 'COMPLETED'
        ) );
        foreach ($indexes as $index) {
            $participatedUsersCompletedByUser[] = array ('USR_UID' => $allAppDbData[$index]['USR_UID'],'DEL_INDEX' => $allAppDbData[$index]['DEL_INDEX']
            );
        }
        // search information of unassigned users
        // the unassigned users are the self service users and groups.
        // the self service users are defined in the TASKs of the PROCESS.
        $unassignedUsers = array ();
        $unassignedGroups = array ();
        //filter only the delegations that are in selfservice status
        // `USR_UID` = '' AND `DEL_FINISH_DATE` IS NULL
        $indexes = $this->aaSearchRecords( $allAppDbData, array ('USR_UID' => 'NULL','DEL_FINISH_DATE' => 'NULL','APP_THREAD_STATUS' => 'OPEN'
        ) );
        foreach ($indexes as $index) {
            $unassignedUsersGroups = array ();
            // use cache
            $oMemcache = PMmemcached::getSingleton( $this->_solrInstance );
            $unassignedUsersGroups = $oMemcache->get( $allAppDbData[$index]['PRO_UID'] . "_" . $allAppDbData[$index]['TAS_UID'] );
            if (! $unassignedUsersGroups) {

                $unassignedUsersGroups = $this->getTaskUnassignedUsersGroupsData( $allAppDbData[$index]['PRO_UID'], $allAppDbData[$index]['TAS_UID'] );

                // if the task has unassigned users or groups add del_index of delegation
                foreach ($unassignedUsersGroups as $i => $newRow) {
                    $unassignedUsersGroups[$i]['DEL_INDEX'] = $allAppDbData[$index]['DEL_INDEX'];
                }
                // store in cache
                $oMemcache->set( $allAppDbData[$index]['PRO_UID'] . "_" . $allAppDbData[$index]['TAS_UID'], $unassignedUsersGroups );
            }

            // copy list of unassigned users and groups
            foreach ($unassignedUsersGroups as $unassignedUserGroup) {
                //unassigned users
                if ($unassignedUserGroup['TU_RELATION'] == 1) {
                    $unassignedUsers[] = array ('USR_UID' => $unassignedUserGroup['USR_UID'],'DEL_INDEX' => $unassignedUserGroup['DEL_INDEX']
                    );
                }                //unassigned groups
                elseif ($unassignedUserGroup['TU_RELATION'] == 2) {
                    $unassignedGroups[] = array ('USR_UID' => $unassignedUserGroup['USR_UID'],'DEL_INDEX' => $unassignedUserGroup['DEL_INDEX']
                    );
                }
            }

        }
        // Get DataTypes of dynaforms
        // use cache array to store the dynaform variables per process
        // use memory array to store information of Datatypes of Dynaforms
        // All the datatypes of the process => all variables in all dynaforms in the
        // process
        $dynaformFieldTypes = array ();

        // get cache instance
        $oMemcache = PMmemcached::getSingleton( $this->_solrInstance );
        $dynaformFieldTypes = $oMemcache->get( $documentInformation['PRO_UID'] );
        if (! $dynaformFieldTypes) {
            G::LoadClass( 'dynaformhandler' );
            $dynaformFileNames = $this->getProcessDynaformFileNames( $documentInformation['PRO_UID'] );
            $dynaformFields = array ();
            foreach ($dynaformFileNames as $dynaformFileName) {
                if (is_file( PATH_DATA . '/sites/workflow/xmlForms/' . $dynaformFileName['DYN_FILENAME'] . '.xml' ) && filesize( PATH_DATA . '/sites/workflow/xmlForms/' . $dynaformFileName['DYN_FILENAME'] . '.xml' ) > 0) {
                    $dyn = new dynaFormHandler( PATH_DATA . '/sites/workflow/xmlForms/' . $dynaformFileName['DYN_FILENAME'] . '.xml' );
                    $dynaformFields[] = $dyn->getFields();
                }
                if (is_file( PATH_DATA . '/sites/workflow/xmlForms/' . $dynaformFileName['DYN_FILENAME'] . '.xml' ) && filesize( PATH_DATA . '/sites/workflow/xmlForms/' . $dynaformFileName['DYN_FILENAME'] . '.xml' ) == 0) {

                    throw new ApplicationWithCorruptDynaformException( "Application with corrupt dynaform. APP_UID: " . $AppUID . "\n" );
                }
            }

            foreach ($dynaformFields as $aDynFormFields) {
                foreach ($aDynFormFields as $field) {
                    // create array of fields and types
                    if ($field->getAttribute( 'validate' ) == 'Int') {
                        $dynaformFieldTypes[$field->nodeName] = 'Int';
                    } elseif ($field->getAttribute( 'validate' ) == 'Real') {
                        $dynaformFieldTypes[$field->nodeName] = 'Real';
                    } else {
                        $dynaformFieldTypes[$field->nodeName] = $field->getAttribute( 'type' );
                    }
                }
            }
            // create cache of dynaformfields
            $oMemcache->set( $documentInformation['PRO_UID'], $dynaformFieldTypes );
        }

        //Return result values
        $result = array(
            $documentInformation,
            $dynaformFieldTypes,
            $lastUpdateDate,
            $maxPriority,
            $assignedUsers,
            $assignedUsersRead,
            $assignedUsersUnread,
            $draftUser,
            $participatedUsers,
            $participatedUsersStartedByUser,
            $participatedUsersCompletedByUser,
            $unassignedUsers,
            $unassignedGroups,
            $pausedUser,
            $cancelledUser
        );

        return $result;
    }

    /**
     * Find the maximun value of the specified column in the array and return the
     * row index
     *
     * @param array $arr array of arrays with the data
     * @param string $column column name to search in
     * @param string $columnType column type STRING, NUMBER, DATE
     * @param string $columnCondition column condition
     * @param string $condition the condition
     * @return integer The index of the maximun record in array
     */
    public function aaGetMaximun ($arr, $column, $columnType = 'STRING', $columnCondition = "", $condition = "")
    {
        // get first value
        $auxValue = $arr[0][$column];
        $index = null;
        foreach ($arr as $i => $row) {
            switch ($columnType) {
                case 'STRING':
                    if ((strnatcmp( $row[$column], $auxValue ) >= 0) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;
                    }
                    break;
                case 'NUMBER':
                    if (($row[$column] >= $auxValue) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;
                    }
                    break;
                case 'DATE':
                    if ((strtotime( $row[$column] ) >= strtotime( $auxValue )) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;
                    }
                    break;
            }
        }
        return $index;
    }

    /**
     * Get minimum of array of arrays
     *
     * @param array $arr array of arrays with the data
     * @param string $column the name of the column to search in
     * @param string $columnType the column type STRING, NUMBER, DATE
     * @param string $columnCondition the column condition
     * @param string $condition the condition
     * @return Ambigous <NULL, unknown> Index of the minimun value found
     */
    public function aaGetMinimun ($arr, $column, $columnType = 'STRING', $columnCondition = "", $condition = "")
    {
        // get first value
        $auxValue = $arr[0][$column];
        $index = null;
        foreach ($arr as $i => $row) {
            switch ($columnType) {
                case 'STRING':
                    if ((strnatcmp( $row[$column], $auxValue ) <= 0) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;
                    }
                    break;
                case 'NUMBER':
                    if (($row[$column] <= $auxValue) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;
                    }
                    break;
                case 'DATE':
                    if ((strtotime( $row[$column] ) <= strtotime( $auxValue )) && (($columnCondition == "") || ($row[$columnCondition] == $condition))) {
                        $auxValue = $row[$column];
                        $index = $i;

                    }
                    break;
            }
        }
        return $index;
    }

    /**
     * Search array of indexes that fullfill the conditions
     *
     * @param array of arrays $arr contains the arrays that are searched
     * @param array $andColumnsConditions contain the conditions that must fullfill 'Column'=>'Condition'
     * @return array of indexes with the found records
     */
    public function aaSearchRecords ($arr, $andColumnsConditions)
    {
        $indexes = array ();
        $isEqual = true;
        foreach ($arr as $i => $row) {
            $evaluateRow = false;
            // evaluate each row
            foreach ($andColumnsConditions as $column => $valueCondition) {
                $condition = $valueCondition;
                $isEqual = true;
                if ($valueCondition == 'NULL') {
                    $isEqual = true;
                    $condition = '';
                }
                if ($valueCondition == 'NOTNULL') {
                    $isEqual = false;
                    $condition = '';
                }
                if ($isEqual) {
                    if ($row[$column] == $condition) {
                        $evaluateRow = true;
                    } else {
                        $evaluateRow = false;
                        break;
                    }
                } else {
                    if ($row[$column] != $condition) {
                        $evaluateRow = true;
                    } else {
                        $evaluateRow = false;
                        break;
                    }
                }
            }
            // add row to indexes
            if ($evaluateRow) {
                $indexes[] = $i;
            }
        }
        return $indexes;
    }

    /**
     * Get application and delegation data from database
     *
     * @param string $AppUID the application identifier
     * @return array of records from database
     */
    public function getApplicationDelegationData ($AppUID)
    {

        $allAppDbData = array ();

        $c = new Criteria();

        $c->addSelectColumn( ApplicationPeer::APP_UID );
        $c->addSelectColumn( ApplicationPeer::APP_NUMBER );
        $c->addSelectColumn( ApplicationPeer::APP_STATUS );
        $c->addSelectColumn( ApplicationPeer::PRO_UID );
        $c->addSelectColumn( ApplicationPeer::APP_CREATE_DATE );
        $c->addSelectColumn( ApplicationPeer::APP_FINISH_DATE );
        $c->addSelectColumn( ApplicationPeer::APP_UPDATE_DATE );
        $c->addSelectColumn( ApplicationPeer::APP_DATA );

        $c->addAsColumn( 'APP_TITLE', 'capp.CON_VALUE' );
        $c->addAsColumn( 'PRO_TITLE', 'cpro.CON_VALUE' );

        $c->addSelectColumn( 'ad.DEL_INDEX' );
        $c->addSelectColumn( 'ad.DEL_PREVIOUS' );
        $c->addSelectColumn( 'ad.TAS_UID' );
        $c->addSelectColumn( 'ad.USR_UID' );
        $c->addSelectColumn( 'ad.DEL_TYPE' );
        $c->addSelectColumn( 'ad.DEL_THREAD' );
        $c->addSelectColumn( 'ad.DEL_THREAD_STATUS' );
        $c->addSelectColumn( 'ad.DEL_PRIORITY' );
        $c->addSelectColumn( 'ad.DEL_DELEGATE_DATE' );
        $c->addSelectColumn( 'ad.DEL_INIT_DATE' );
        $c->addSelectColumn( 'ad.DEL_TASK_DUE_DATE' );
        $c->addSelectColumn( 'ad.DEL_FINISH_DATE' );
        $c->addSelectColumn( 'ad.DEL_DURATION' );
        $c->addSelectColumn( 'ad.DEL_QUEUE_DURATION' );
        $c->addSelectColumn( 'ad.DEL_DELAY_DURATION' );
        $c->addSelectColumn( 'ad.DEL_STARTED' );
        $c->addSelectColumn( 'ad.DEL_FINISHED' );
        $c->addSelectColumn( 'ad.DEL_DELAYED' );
        $c->addSelectColumn( 'ad.APP_OVERDUE_PERCENTAGE' );

        $c->addSelectColumn( 'at.APP_THREAD_INDEX' );
        $c->addSelectColumn( 'at.APP_THREAD_PARENT' );
        $c->addSelectColumn( 'at.APP_THREAD_STATUS' );

        $c->addAlias( 'capp', 'CONTENT' );
        $c->addAlias( 'cpro', 'CONTENT' );
        $c->addAlias( 'ad', 'APP_DELEGATION' );
        $c->addAlias( 'at', 'APP_THREAD' );

        $aConditions = array ();
        $aConditions[] = array (ApplicationPeer::APP_UID,'capp.CON_ID'
        );
        $aConditions[] = array ('capp.CON_CATEGORY',DBAdapter::getStringDelimiter() . 'APP_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array ('capp.CON_LANG',DBAdapter::getStringDelimiter() . 'en' . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $aConditions = array ();
        $aConditions[] = array (ApplicationPeer::PRO_UID,'cpro.CON_ID'
        );
        $aConditions[] = array ('cpro.CON_CATEGORY',DBAdapter::getStringDelimiter() . 'PRO_TITLE' . DBAdapter::getStringDelimiter()
        );
        $aConditions[] = array ('cpro.CON_LANG',DBAdapter::getStringDelimiter() . 'en' . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC( $aConditions, Criteria::LEFT_JOIN );

        $c->addJoin( ApplicationPeer::APP_UID, 'ad.APP_UID', Criteria::JOIN );

        $aConditions = array ();
        $aConditions[] = array ('ad.APP_UID','at.APP_UID'
        );
        $aConditions[] = array ('ad.DEL_THREAD','at.APP_THREAD_INDEX'
        );
        $c->addJoinMC( $aConditions, Criteria::JOIN );

        $c->add( ApplicationPeer::APP_UID, $AppUID );

        $rs = ApplicationPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rs->next();
        $row = $rs->getRow();

        while (is_array( $row )) {
            $allAppDbData[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $allAppDbData;
    }

    /**
     * Get application and delegation data from database
     *
     * @param string $aAppUID array of application identifiers
     * @return array of array of records from database
     */
    public function getListApplicationUpdateDelegationData($aaAppUIDs)
    {
        $allAppDbData = array();

        $c = new Criteria();

        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addSelectColumn(ApplicationPeer::PRO_UID);
        $c->addSelectColumn(ApplicationPeer::APP_CREATE_DATE);
        $c->addSelectColumn(ApplicationPeer::APP_FINISH_DATE);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(ApplicationPeer::APP_DATA);

        $c->addAsColumn("APP_TITLE", "capp.CON_VALUE");
        $c->addAsColumn("PRO_TITLE", "cpro.CON_VALUE");

        $c->addSelectColumn("ad.DEL_INDEX");
        $c->addSelectColumn("ad.DEL_PREVIOUS");
        $c->addSelectColumn("ad.TAS_UID");
        $c->addSelectColumn("ad.USR_UID");
        $c->addSelectColumn("ad.DEL_TYPE");
        $c->addSelectColumn("ad.DEL_THREAD");
        $c->addSelectColumn("ad.DEL_THREAD_STATUS");
        $c->addSelectColumn("ad.DEL_PRIORITY");
        $c->addSelectColumn("ad.DEL_DELEGATE_DATE");
        $c->addSelectColumn("ad.DEL_INIT_DATE");
        $c->addSelectColumn("ad.DEL_TASK_DUE_DATE");
        $c->addSelectColumn("ad.DEL_FINISH_DATE");
        $c->addSelectColumn("ad.DEL_DURATION");
        $c->addSelectColumn("ad.DEL_QUEUE_DURATION");
        $c->addSelectColumn("ad.DEL_DELAY_DURATION");
        $c->addSelectColumn("ad.DEL_STARTED");
        $c->addSelectColumn("ad.DEL_FINISHED");
        $c->addSelectColumn("ad.DEL_DELAYED");
        $c->addSelectColumn("ad.APP_OVERDUE_PERCENTAGE");

        $c->addSelectColumn("at.APP_THREAD_INDEX");
        $c->addSelectColumn("at.APP_THREAD_PARENT");
        $c->addSelectColumn("at.APP_THREAD_STATUS");

        $c->addAsColumn("APPDELY_APP_DELAY_UID", "appDely.APP_DELAY_UID");
        $c->addAsColumn("APPDELY_APP_TYPE", "appDely.APP_TYPE");
        $c->addAsColumn("APPDELY_APP_DISABLE_ACTION_USER", "appDely.APP_DISABLE_ACTION_USER");

        $c->addAsColumn("PRO_CATEGORY_UID", "pro.PRO_CATEGORY");

        $c->addAlias("capp", "CONTENT");
        $c->addAlias("cpro", "CONTENT");
        $c->addAlias("ad", "APP_DELEGATION");
        $c->addAlias("at", "APP_THREAD");
        $c->addAlias("appDely", "APP_DELAY");
        $c->addAlias("pro", "PROCESS");

        $arrayCondition = array();
        $arrayCondition[] = array(ApplicationPeer::APP_UID, "capp.CON_ID");
        $arrayCondition[] = array("capp.CON_CATEGORY", DBAdapter::getStringDelimiter() . "APP_TITLE" . DBAdapter::getStringDelimiter());
        $arrayCondition[] = array("capp.CON_LANG", DBAdapter::getStringDelimiter() . "en" . DBAdapter::getStringDelimiter());
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(ApplicationPeer::PRO_UID, "cpro.CON_ID");
        $arrayCondition[] = array("cpro.CON_CATEGORY", DBAdapter::getStringDelimiter() . "PRO_TITLE" . DBAdapter::getStringDelimiter());
        $arrayCondition[] = array("cpro.CON_LANG", DBAdapter::getStringDelimiter() . "en" . DBAdapter::getStringDelimiter());
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $c->addJoin(ApplicationPeer::APP_UID, "ad.APP_UID", Criteria::JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array("ad.APP_UID", "at.APP_UID");
        $arrayCondition[] = array("ad.DEL_THREAD", "at.APP_THREAD_INDEX");
        $c->addJoinMC($arrayCondition, Criteria::JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array("ad.APP_UID", "appDely.APP_UID");
        $arrayCondition[] = array("ad.DEL_INDEX", "appDely.APP_DEL_INDEX");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $arrayCondition = array();
        $arrayCondition[] = array(ApplicationPeer::PRO_UID, "pro.PRO_UID");
        $c->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $c->add(ApplicationPeer::APP_UID, $aaAppUIDs, Criteria::IN);

        $rs = ApplicationPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $row = $rs->getRow();

            $allAppDbData[] = $row;
        }

        return $allAppDbData;
    }

    /**
     * Get the list of groups of unassigned users of the specified task from
     * database
     *
     * @param string $ProUID Process identifier
     * @param string $TaskUID task identifier
     * @return array of unassigned user groups
     */
    public function getTaskUnassignedUsersGroupsData ($ProUID, $TaskUID)
    {
        $unassignedUsersGroups = array ();

        $c = new Criteria();

        $c->addSelectColumn( TaskUserPeer::USR_UID );
        $c->addSelectColumn( TaskUserPeer::TU_RELATION );

        $aConditions = array ();
        $aConditions[] = array (TaskPeer::TAS_UID,TaskUserPeer::TAS_UID
        );
        $aConditions[] = array (TaskPeer::TAS_ASSIGN_TYPE,DBAdapter::getStringDelimiter() . 'SELF_SERVICE' . DBAdapter::getStringDelimiter()
        );
        $c->addJoinMC( $aConditions, Criteria::JOIN );

        $c->add( TaskPeer::PRO_UID, $ProUID );
        $c->add( TaskPeer::TAS_UID, $TaskUID );

        $rs = TaskPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        // echo $c->toString();
        $rs->next();
        $row = $rs->getRow();

        while (is_array( $row )) {
            $unassignedUsersGroups[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }

        return $unassignedUsersGroups;
    }

    /**
     * Get the list of dynaform file names associated with the specified process
     * from database
     *
     * @param string $ProUID process identifier
     * @return array of dynaform file names
     */
    public function getProcessDynaformFileNames ($ProUID)
    {
        $dynaformFileNames = array ();

        $c = new Criteria();

        $c->addSelectColumn( DynaformPeer::DYN_FILENAME );

        $c->add( DynaformPeer::PRO_UID, $ProUID );

        $rs = DynaformPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();

        while (is_array( $row )) {
            $dynaformFileNames[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }

        return $dynaformFileNames;
    }

    /**
     * Store a flag indicating if the application was updated in database
     * table APP_SOLR_QUEUE
     *
     * @param string $AppUid applicatiom identifier
     * @param integer $updated 0:false, not updated, 1: updated, 2:deleted
     */
    public function applicationChangedUpdateSolrQueue ($AppUid, $updated)
    {
        $oAppSolrQueue = new AppSolrQueue();

        $oAppSolrQueue->createUpdate( $AppUid, $updated );
    }

    /**
     * Update application records in Solr that are stored in APP_SOLR_QUEUE table
     */
    public function synchronizePendingApplications ()
    {
        // check table of pending updates
        $oAppSolrQueue = new AppSolrQueue();

        $aAppSolrQueue = $oAppSolrQueue->getListUpdatedApplications();

        foreach ($aAppSolrQueue as $oAppSolrQueueEntity) {
            // call the syncronization function
            if ($oAppSolrQueueEntity->appUpdated == 1) {
                $this->updateApplicationSearchIndex( $oAppSolrQueueEntity->appUid );
            }
            if ($oAppSolrQueueEntity->appUpdated == 2) {
                $this->deleteApplicationSearchIndex( $oAppSolrQueueEntity->appUid );
            }
            $this->applicationChangedUpdateSolrQueue( $oAppSolrQueueEntity->appUid, 0 );
        }
    }

    /**
     * Get the total number of application records in database
     *
     * @return integer application counter
     */
    public function getCountApplicationsPMOS2 ()
    {
        $c = new Criteria();

        $c->addSelectColumn( ApplicationPeer::APP_UID );

        $count = ApplicationPeer::doCount( $c );

        return $count;
    }

    /**
     * Get the total number of application records in search index
     *
     * @return integer application counter
     */
    public function getCountApplicationsSearchIndex ()
    {
        G::LoadClass( 'searchIndex' );

        $searchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );
        // execute query
        $count = $searchIndex->getNumberDocuments( $this->_solrInstance );

        return $count;
    }

    /**
     * Optimize the records in search index
     *
     * @return
     *
     */
    public function optimizeSearchIndex ()
    {
        G::LoadClass( 'searchIndex' );

        $searchIndex = new BpmnEngine_Services_SearchIndex( $this->_solrIsEnabled, $this->_solrHost );
        // execute query
        $searchIndex->optimizeIndexChanges( $this->_solrInstance );

    }

    /**
     * Get a paginated list of application uids from database.
     *
     * @param integer $skip the offset from where to return the application records
     * @param integer $pagesize the size of the page
     * @return array of application id's in the specified page.
     */
    public function getPagedApplicationUids ($skip, $pagesize)
    {

        $c = new Criteria();

        $c->addSelectColumn( ApplicationPeer::APP_UID );
        $c->setOffset( $skip );
        $c->setLimit( $pagesize );

        $rs = ApplicationPeer::doSelectRS( $c );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $rs->next();
        $row = $rs->getRow();
        $appUIds = array ();
        while (is_array( $row )) {
            $appUIds[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $appUIds;
    }

    /**
     * Reindex all the application records in Solr server
     * update applications in groups of 1000
     */
    public function reindexAllApplications ($SkipRecords = 0, $indexTrunkSize = 1000)
    {
        $trunk = $indexTrunkSize;
        // delete all documents to begin reindex
        // deleteAllDocuments();
        // commitChanges();
        // print "Deleted all documents \n";
        // search trunks of id's to regenerate index
        $numRows = $this->getCountApplicationsPMOS2();
        print "Total number of records: " . $numRows . "\n";
        $initTimeAll = microtime( true );

        for ($skip = $SkipRecords; $skip <= $numRows; true) {
            $aaAPPUIds = $this->getPagedApplicationUids( $skip, $trunk );

            printf( "Indexing %d to %d \n", $skip, $skip + $trunk );
            $initTimeDoc = microtime( true );
            $this->updateApplicationSearchIndex( $aaAPPUIds );

            $curTimeDoc = gmdate( 'H:i:s', (microtime( true ) - $initTimeDoc) );
            printf( "Indexing document time: %s \n", $curTimeDoc );
            $skip += $trunk;
        }

        $curTimeDoc = gmdate( 'H:i:s', (microtime( true ) - $initTimeAll) );
        printf( "Total reindex time: %s \n", $curTimeDoc );
        printf( "Reindex completed successfully!!.\n" );
    }
}

