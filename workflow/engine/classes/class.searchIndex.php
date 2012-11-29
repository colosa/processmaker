<?php

/**
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
 */

/**
 * Class used as interface to have access to the search index services
 *
 * @author Herbert Saal Gutierrez
 *
 */
class BpmnEngine_Services_SearchIndex
{
    private $_solrIsEnabled = false;
    private $_solrHost = "";

    public function __construct ($solrIsEnabled = false, $solrHost = "")
    {
        // check if Zend Library is available
        // if(class_exists("Zend_Registry")){
        // $registry = Zend_Registry::getInstance();
        // //check if configuration is enabled
        // $this->solrIsEnabled = $registry->isRegistered('solrEnabled') &&
        // $registry->get('solrEnabled') == 1;
        // $this->solrHost =
        // $registry->isRegistered('solrHost')?$registry->get('solrHost'):"";
        // }
        // else{
        // //use the parameters to initialize class
        $this->_solrIsEnabled = $solrIsEnabled;
        $this->_solrHost = $solrHost;
        // }
    }

    /**
     * Verify if the Solr service is available
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * no input parameters @param[in]
     *
     * @param [out] bool true if index service is enabled false in other case
     */
    public function isEnabled ()
    {
        // require_once (ROOT_PATH .
        // '/businessLogic/modules/SearchIndexAccess/Solr.php');
        require_once ('class.solr.php');
        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );
        return $solr->isEnabled();
    }

    /**
     * Get the list of facets in base to the specified query and filter
     * @gearman = true
     * @rest = false
     * @background = false
     *
     * @param [in] Entity_FacetRequest facetRequestEntity Facet request entity
     * @param [out] array FacetGroup
     */
    public function getFacetsList ($facetRequestEntity)
    {
        require_once ('class.solr.php');
        // require_once (ROOT_PATH .
        // '/businessLogic/modules/SearchIndexAccess/Solr.php');
        require_once ('entities/FacetGroup.php');
        require_once ('entities/FacetItem.php');
        require_once ('entities/SelectedFacetGroupItem.php');
        require_once ('entities/FacetResult.php');

        /**
         * ***************************************************************
         */
        // get array of selected facet groups
        $facetRequestEntity->selectedFacetsString = str_replace( ',,', ',', $facetRequestEntity->selectedFacetsString );
        // remove descriptions of selected facet groups


        $aGroups = explode( ',', $facetRequestEntity->selectedFacetsString );

        $aGroups = array_filter( $aGroups ); // remove empty items


        $aSelectedFacetGroups = array ();
        foreach ($aGroups as $i => $value) {
            $gi = explode( ':::', $value );
            $gr = explode( '::', $gi[0] );
            $it = explode( '::', $gi[1] );

            // create string for remove condition
            $count = 0;
            $removeCondition = str_replace( $value . ',', '', $facetRequestEntity->selectedFacetsString, $count );
            if ($count == 0) {
                $removeCondition = str_replace( $value, '', $facetRequestEntity->selectedFacetsString, $count );
            }
            $selectedFacetGroupData = array ('selectedFacetGroupName' => $gr[0],'selectedFacetGroupPrintName' => $gr[1],'selectedFacetItemName' => $it[0],'selectedFacetItemPrintName' => $it[1],'selectedFacetRemoveCondition' => $removeCondition
            );

            $aSelectedFacetGroups[] = Entity_SelectedFacetGroupItem::createForRequest( $selectedFacetGroupData );
        }

        /**
         * ***************************************************************
         */
        // convert request to index request
        // create filters
        $filters = array ();
        if (! empty( $aSelectedFacetGroups )) {
            // exclude facetFields and facetDates included in filter from the next
            // list of facets
            foreach ($aSelectedFacetGroups as $value) {
                $facetRequestEntity->facetFields = array_diff( $facetRequestEntity->facetFields, array ($value->selectedFacetGroupName
                ) );
                $facetRequestEntity->facetDates = array_diff( $facetRequestEntity->facetDates, array ($value->selectedFacetGroupName
                ) );
            }

            // $facetFields = array_diff($facetFields,
            // $facetInterfaceRequestEntity->selectedFacetGroups);
            // $facetDates = array_diff($facetDates,
            // $facetInterfaceRequestEntity->selectedFacetGroups);
            foreach ($aSelectedFacetGroups as $group) {
                $filters[] = $group->selectedFacetGroupName . ':' . urlencode( $group->selectedFacetItemName );
            }
        }
        $facetRequestEntity->filters = $filters;

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // create list of facets
        $facetsList = $solr->getFacetsList( $facetRequestEntity );

        $numFound = $facetsList->response->numFound;

        $facetCounts = $facetsList->facet_counts;

        $facetGroups = array ();
        // convert facet fields result to objects
        /**
         * *********************************************************************
         */
        // include facet field results
        $facetFieldsResult = $facetsList->facet_counts->facet_fields;
        if (! empty( $facetFieldsResult )) {
            foreach ($facetFieldsResult as $facetGroup => $facetvalues) {
                if (count( $facetvalues ) > 0)                 // if the group have facets included
{
                    $data = array ('facetGroupName' => $facetGroup
                    );
                    $data['facetGroupPrintName'] = $facetGroup;
                    $data['facetGroupType'] = 'field';
                    $facetItems = array ();
                    for ($i = 0; $i < count( $facetvalues ); $i += 2) {
                        $dataItem = array ();
                        $dataItem['facetName'] = $facetvalues[$i];
                        $dataItem['facetPrintName'] = $facetvalues[$i];
                        $dataItem['facetCount'] = $facetvalues[$i + 1];
                        $dataItem['facetSelectCondition'] = $facetRequestEntity->selectedFacetsString . (empty( $facetRequestEntity->selectedFacetsString ) ? '' : ',') . $data['facetGroupName'] . '::' . $data['facetGroupPrintName'] . ':::' . $dataItem['facetName'] . '::' . $dataItem['facetPrintName'];
                        $newFacetItem = Entity_FacetItem::createForInsert( $dataItem );
                        $facetItems[] = $newFacetItem;
                    }
                    $data['facetItems'] = $facetItems;
                    $newFacetGroup = Entity_FacetGroup::createForInsert( $data );

                    $facetGroups[] = $newFacetGroup;
                }
            }
        }
        /**
         * *********************************************************************
         */
        // include facet date ranges results
        $facetDatesResult = $facetsList->facet_counts->facet_dates;
        if (! empty( $facetDatesResult )) {
            foreach ($facetDatesResult as $facetGroup => $facetvalues) {
                if (count( (array) $facetvalues ) > 3)                 // if the group have any facets included
                // besides start, end and gap
                {
                    $data = array ('facetGroupName' => $facetGroup
                    );
                    $data['facetGroupPrintName'] = $facetGroup;
                    $data['facetGroupType'] = 'daterange';
                    $facetItems = array ();
                    $facetvalueskeys = array_keys( (array) $facetvalues );
                    foreach ($facetvalueskeys as $i => $k) {
                        if ($k != 'gap' && $k != 'start' && $k != 'end') {
                            $dataItem = array ();
                            if ($i < count( $facetvalueskeys ) - 4) {

                                $dataItem['facetName'] = '[' . $k . '%20TO%20' . $facetvalueskeys[$i + 1] . ']';
                                $dataItem['facetPrintName'] = '[' . $k . '%20TO%20' . $facetvalueskeys[$i + 1] . ']';
                            } else {
                                // the last group
                                $dataItem['facetName'] = '[' . $k . '%20TO%20' . $facetvalues->end . ']';
                                $dataItem['facetPrintName'] = '[' . $k . '%20TO%20' . $facetvalues->end . ']';
                            }

                            $dataItem['facetCount'] = $facetvalues->$k;
                            $dataItem['facetSelectCondition'] = $facetRequestEntity->selectedFacetsString . (empty( $facetRequestEntity->selectedFacetsString ) ? '' : ',') . $data['facetGroupName'] . '::' . $data['facetGroupPrintName'] . ':::' . $dataItem['facetName'] . '::' . $dataItem['facetPrintName'];
                            $newFacetItem = Entity_FacetItem::createForInsert( $dataItem );
                            $facetItems[] = $newFacetItem;
                        }
                    }

                    $data['facetItems'] = $facetItems;
                    $newFacetGroup = Entity_FacetGroup::createForInsert( $data );

                    $facetGroups[] = $newFacetGroup;
                }
            }
        }
        // TODO:convert facet queries
        // -----
        /**
         * ***************************************************************
         */
        // Create a filter string used in the filter of results of a datatable
        $filterText = ''; // the list of selected filters used for filtering result,
        // send in ajax
        foreach ($aSelectedFacetGroups as $selectedFacetGroup) {
            $filterText .= $selectedFacetGroup->selectedFacetGroupName . ':' . urlencode( $selectedFacetGroup->selectedFacetItemName ) . ',';
        }
        $filterText = substr_replace( $filterText, '', - 1 );
        // $filterText = ($filterText == '')?'':'&filterText='.$filterText;


        /**
         * ***************************************************************
         */
        // Create result
        $dataFacetResult = array ('aFacetGroups' => $facetGroups,'aSelectedFacetGroups' => $aSelectedFacetGroups,'sFilterText' => $filterText
        );
        $facetResult = Entity_FacetResult::createForRequest( $dataFacetResult );

        return $facetResult;
    }

    /**
     * Get the total number of documents in search server
     *
     * @param string $workspace
     * @return integer number of documents
     *
     */
    public function getNumberDocuments ($workspace)
    {
        require_once ('class.solr.php');
        // require_once (ROOT_PATH .
        // '/businessLogic/modules/SearchIndexAccess/Solr.php');
        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // create list of facets
        $numberDocuments = $solr->getNumberDocuments( $workspace );

        return $numberDocuments;
    }

    /**
     * Update document Index
     *
     * @param SolrUpdateDocumentEntity $solrUpdateDocumentEntity
     */
    public function updateIndexDocument ($solrUpdateDocumentEntity)
    {
        G::LoadClass( 'solr' );

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // create list of facets
        $solr->updateDocument( $solrUpdateDocumentEntity );
    }

    /**
     * Delete document from index
     *
     * @param string $workspace
     * @param string $idQuery
     */
    public function deleteDocumentFromIndex ($workspace, $idQuery)
    {
        G::LoadClass( 'solr' );

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // create list of facets
        $solr->deleteDocument( $workspace, $idQuery );
    }

    /**
     * Commit index changes
     *
     * @param string $workspace
     */
    public function commitIndexChanges ($workspace)
    {
        G::LoadClass( 'solr' );

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // commit
        $solr->commitChanges( $workspace );
    }

    /**
     * Optimize index changes
     *
     * @param string $workspace
     */
    public function optimizeIndexChanges ($workspace)
    {
        G::LoadClass( 'solr' );

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // commit
        $solr->optimizeChanges( $workspace );
    }

    /**
     * Call Solr server to return the list of paginated pages.
     *
     * @param FacetRequest $solrRequestData
     * @return Entity_SolrQueryResult
     */
    public function getDataTablePaginatedList ($solrRequestData)
    {
        require_once ('class.solr.php');
        require_once ('entities/SolrRequestData.php');
        require_once ('entities/SolrQueryResult.php');

        // prepare the list of sorted columns
        // verify if the data of sorting is available
        if (isset( $solrRequestData->sortCols[0] )) {
            for ($i = 0; $i < $solrRequestData->numSortingCols; $i ++) {
                // verify if column is sortable
                if ($solrRequestData->includeCols[$solrRequestData->sortCols[$i]] != '' && $solrRequestData->sortableCols[$i] == "true") {
                    // change sorting column index to column names
                    $solrRequestData->sortCols[$i] = $solrRequestData->includeCols[$solrRequestData->sortCols[$i]];
                    // define the direction of the sorting columns
                    $solrRequestData->sortDir[$i] = $solrRequestData->sortDir[$i];
                }
            }
        }
        // remove placeholder fields
        // the placeholder doesn't affect the the solr's response
        // $solrRequestData->includeCols = array_diff($solrRequestData->includeCols,
        // array(''));


        // execute query
        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );
        $solrPaginatedResult = $solr->executeQuery( $solrRequestData );

        // get total number of documents in index
        $numTotalDocs = $solr->getNumberDocuments( $solrRequestData->workspace );

        // create the Datatable response of the query
        $numFound = $solrPaginatedResult->response->numFound;

        $docs = $solrPaginatedResult->response->docs;
        // print_r($docs);
        // insert list of names in docs result
        $data = array ("sEcho" => '',        // must be completed in response
"iTotalRecords" => intval( $numTotalDocs ),        // we must get the
        // total number of
        // documents
        "iTotalDisplayRecords" => $numFound,"aaData" => array ()
        );
        // copy result document or add placeholders to result
        foreach ($docs as $i => $doc) {
            $data['aaData'][$i] = array ();
            foreach ($solrRequestData->includeCols as $columnName) {
                if ($columnName == '') {
                    $data['aaData'][$i][] = ''; // placeholder
                } else {
                    if (isset( $doc->$columnName )) {
                        $data['aaData'][$i][] = $doc->$columnName;
                    } else {
                        $data['aaData'][$i][] = '';
                    }
                }
            }
        }

        $solrQueryResponse = Entity_SolrQueryResult::createForRequest( $data );
        //


        return $solrQueryResponse;
    }

    /**
     * Return the list of stored fields in the index.
     *
     * @param string $workspace
     * @return array of index fields
     */
    public function getIndexFields ($workspace)
    {
        require_once ('class.solr.php');

        $solr = new BpmnEngine_SearchIndexAccess_Solr( $this->_solrIsEnabled, $this->_solrHost );

        // print "SearchIndex!!!!";
        // create list of facets
        $solrFieldsData = $solr->getListIndexedStoredFields( $workspace );
        // copy list of arrays
        $listFields = array ();
        foreach ($solrFieldsData->fields as $key => $fieldData) {
            if (array_key_exists( 'dynamicBase', $fieldData )) {
                $originalFieldName = substr( $key, 0, - strlen( $fieldData->dynamicBase ) + 1 );
                // $listFields[strtolower($originalFieldName)] = $key; //in case of case insentive strings
                // Maintain case sensitive variable names
                $listFields[$originalFieldName] = $key;
            } else {
                // $listFields[strtolower($key)] = $key;
                // Maintain case sensitive variable names
                $listFields[$key] = $key;
            }
        }

        return $listFields;
    }

}