<?php

/**
 * Class used as interface to have access to the search index services
 */
class BpmnEngineServicesSearchIndex
{
    private $_solrIsEnabled = false;
    private $_solrHost = "";

    public function __construct($solrIsEnabled = false, $solrHost = "")
    {
        $this->_solrIsEnabled = $solrIsEnabled;
        $this->_solrHost = $solrHost;
    }

    /**
     * Verify if the Solr service is available
     *
     * @gearman = false
     * @rest = false
     * @background = false no input parameters @param[in]
     * @param [out] bool true if index service is enabled false in other case
     */
    public function isEnabled($workspace)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);
        return $solr->isEnabled($workspace);
    }

    /**
     * Get the list of facets in base to the specified query and filter
     *
     * @gearman = true
     * @rest = false
     * @background = false
     * @param [in] Entity_FacetRequest facetRequestEntity Facet request entity
     * @param [out] array FacetGroup
     */
    public function getFacetsList($facetRequestEntity)
    {
        // get array of selected facet groups
        $facetRequestEntity->selectedFacetsString = str_replace(',,', ',', $facetRequestEntity->selectedFacetsString);
        // remove descriptions of selected facet groups

        $aGroups = explode(',', $facetRequestEntity->selectedFacetsString);

        $aGroups = array_filter($aGroups); // remove empty items

        $aSelectedFacetGroups = array();
        foreach ($aGroups as $i => $value) {
            $gi = explode(':::', $value);
            $gr = explode('::', $gi [0]);
            $it = explode('::', $gi [1]);

            // create string for remove condition
            $count = 0;
            $removeCondition = str_replace($value . ',', '', $facetRequestEntity->selectedFacetsString, $count);
            if ($count == 0) {
                $removeCondition = str_replace($value, '', $facetRequestEntity->selectedFacetsString, $count);
            }
            $selectedFacetGroupData = array(
                'selectedFacetGroupName' => $gr [0],
                'selectedFacetGroupPrintName' => $gr [1],
                'selectedFacetItemName' => $it [0],
                'selectedFacetItemPrintName' => $it [1],
                'selectedFacetRemoveCondition' => $removeCondition
            );

            $aSelectedFacetGroups [] = EntitySelectedFacetGroupItem::createForRequest($selectedFacetGroupData);
        }

        // convert request to index request
        // create filters
        $filters = array();
        if (!empty($aSelectedFacetGroups)) {
            // exclude facetFields and facetDates included in filter from the next
            // list of facets
            foreach ($aSelectedFacetGroups as $value) {
                $facetRequestEntity->facetFields = array_diff($facetRequestEntity->facetFields, array(
                    $value->selectedFacetGroupName
                ));
                $facetRequestEntity->facetDates = array_diff($facetRequestEntity->facetDates, array(
                    $value->selectedFacetGroupName
                ));
            }

            foreach ($aSelectedFacetGroups as $group) {
                $filters [] = $group->selectedFacetGroupName . ':' . urlencode($group->selectedFacetItemName);
            }
        }
        $facetRequestEntity->filters = $filters;

        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // create list of facets
        $facetsList = $solr->getFacetsList($facetRequestEntity);

        $numFound = $facetsList->response->numFound;

        $facetCounts = $facetsList->facet_counts;

        $facetGroups = array();

        // convert facet fields result to objects
        // include facet field results
        $facetFieldsResult = $facetsList->facet_counts->facet_fields;
        if (!empty($facetFieldsResult)) {
            foreach ($facetFieldsResult as $facetGroup => $facetvalues) {
                if (count($facetvalues) > 0) {         // if the group have facets included
                    $data = array(
                        'facetGroupName' => $facetGroup
                    );
                    $data ['facetGroupPrintName'] = $facetGroup;
                    $data ['facetGroupType'] = 'field';
                    $facetItems = array();
                    for ($i = 0; $i < count($facetvalues); $i += 2) {
                        $dataItem = array();
                        $dataItem ['facetName'] = $facetvalues [$i];
                        $dataItem ['facetPrintName'] = $facetvalues [$i];
                        $dataItem ['facetCount'] = $facetvalues [$i + 1];
                        $dataItem ['facetSelectCondition'] = $facetRequestEntity->selectedFacetsString . (empty($facetRequestEntity->selectedFacetsString) ? '' : ',') . $data ['facetGroupName'] . '::' . $data ['facetGroupPrintName'] . ':::' . $dataItem ['facetName'] . '::' . $dataItem ['facetPrintName'];
                        $newFacetItem = EntityFacetItem::createForInsert($dataItem);
                        $facetItems [] = $newFacetItem;
                    }
                    $data ['facetItems'] = $facetItems;
                    $newFacetGroup = EntityFacetGroup::createForInsert($data);

                    $facetGroups [] = $newFacetGroup;
                }
            }
        }

        // include facet date ranges results
        $facetDatesResult = $facetsList->facet_counts->facet_dates;
        if (!empty($facetDatesResult)) {
            foreach ($facetDatesResult as $facetGroup => $facetvalues) {
                if (count((array) $facetvalues) > 3) {         // if the group have any facets included
                    // besides start, end and gap
                    $data = array(
                        'facetGroupName' => $facetGroup
                    );
                    $data ['facetGroupPrintName'] = $facetGroup;
                    $data ['facetGroupType'] = 'daterange';
                    $facetItems = array();
                    $facetvalueskeys = array_keys((array) $facetvalues);
                    foreach ($facetvalueskeys as $i => $k) {
                        if ($k != 'gap' && $k != 'start' && $k != 'end') {
                            $dataItem = array();
                            if ($i < count($facetvalueskeys) - 4) {
                                $dataItem ['facetName'] = '[' . $k . '%20TO%20' . $facetvalueskeys [$i + 1] . ']';
                                $dataItem ['facetPrintName'] = '[' . $k . '%20TO%20' . $facetvalueskeys [$i + 1] . ']';
                            } else {
                                // the last group
                                $dataItem ['facetName'] = '[' . $k . '%20TO%20' . $facetvalues->end . ']';
                                $dataItem ['facetPrintName'] = '[' . $k . '%20TO%20' . $facetvalues->end . ']';
                            }

                            $dataItem ['facetCount'] = $facetvalues->$k;
                            $dataItem ['facetSelectCondition'] = $facetRequestEntity->selectedFacetsString . (empty($facetRequestEntity->selectedFacetsString) ? '' : ',') . $data ['facetGroupName'] . '::' . $data ['facetGroupPrintName'] . ':::' . $dataItem ['facetName'] . '::' . $dataItem ['facetPrintName'];
                            $newFacetItem = EntityFacetItem::createForInsert($dataItem);
                            $facetItems [] = $newFacetItem;
                        }
                    }

                    $data ['facetItems'] = $facetItems;
                    $newFacetGroup = EntityFacetGroup::createForInsert($data);

                    $facetGroups [] = $newFacetGroup;
                }
            }
        }
        // TODO:convert facet queries
        // Create a filter string used in the filter of results of a datatable
        $filterText = ''; // the list of selected filters used for filtering result,
        // send in ajax
        foreach ($aSelectedFacetGroups as $selectedFacetGroup) {
            $filterText .= $selectedFacetGroup->selectedFacetGroupName . ':' . urlencode($selectedFacetGroup->selectedFacetItemName) . ',';
        }
        $filterText = substr_replace($filterText, '', - 1);

        // Create result
        $dataFacetResult = array(
            'aFacetGroups' => $facetGroups,
            'aSelectedFacetGroups' => $aSelectedFacetGroups,
            'sFilterText' => $filterText
        );
        $facetResult = EntityFacetResult::createForRequest($dataFacetResult);

        return $facetResult;
    }

    /**
     * Get the total number of documents in search server
     * @param string $workspace
     * @return integer number of documents
     *
     */
    public function getNumberDocuments($workspace)
    {
        require_once('class.solr.php');
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // create list of facets
        $numberDocuments = $solr->getNumberDocuments($workspace);

        return $numberDocuments;
    }

    /**
     * Update document Index
     * @param SolrUpdateDocumentEntity $solrUpdateDocumentEntity
     */
    public function updateIndexDocument($solrUpdateDocumentEntity)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // create list of facets
        $solr->updateDocument($solrUpdateDocumentEntity);
    }

    /**
     * Delete document from index
     * @param string $workspace
     * @param string $idQuery
     */
    public function deleteDocumentFromIndex($workspace, $idQuery)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // create list of facets
        $solr->deleteDocument($workspace, $idQuery);
    }

    /**
     * Commit index changes
     * @param string $workspace
     */
    public function commitIndexChanges($workspace)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // commit
        $solr->commitChanges($workspace);
    }

    /**
     * Optimize index changes
     * @param string $workspace
     */
    public function optimizeIndexChanges($workspace)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        // commit
        $solr->optimizeChanges($workspace);
    }

    /**
     * Call Solr server to return the list of paginated pages.
     * @param FacetRequest $solrRequestData
     * @return EntitySolrQueryResult
     */
    public function getDataTablePaginatedList($solrRequestData)
    {
        // execute query
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);
        $solrPaginatedResult = $solr->executeQuery($solrRequestData);

        // get total number of documents in index
        $numTotalDocs = $solr->getNumberDocuments($solrRequestData->workspace);

        // create the Datatable response of the query
        $numFound = $solrPaginatedResult->response->numFound;

        $docs = $solrPaginatedResult->response->docs;

        // insert list of names in docs result
        $data = array(
            "sEcho" => '', // must be completed in response
            "iTotalRecords" => intval($numTotalDocs), // we must get the
            // total number of documents
            "iTotalDisplayRecords" => $numFound,
            "aaData" => array()
        );
        // copy result document or add placeholders to result
        foreach ($docs as $i => $doc) {
            $data ['aaData'] [$i] = array();
            foreach ($solrRequestData->includeCols as $columnName) {
                if ($columnName == '') {
                    $data ['aaData'] [$i] [] = ''; // placeholder
                } else {
                    if (isset($doc->$columnName)) {
                        $data ['aaData'] [$i] [$columnName] = $doc->$columnName;
                    } else {
                        $data ['aaData'] [$i] [$columnName] = '';
                    }
                }
            }
        }

        $solrQueryResponse = EntitySolrQueryResult::createForRequest($data);

        return $solrQueryResponse;
    }

    /**
     * Return the list of stored fields in the index.
     * @param string $workspace
     * @return array of index fields
     */
    public function getIndexFields($workspace)
    {
        $solr = new BpmnEngineSearchIndexAccessSolr($this->_solrIsEnabled, $this->_solrHost);

        $solrFieldsData = $solr->getListIndexedStoredFields($workspace);
        // copy list of arrays
        $listFields = array();
        foreach ($solrFieldsData->fields as $key => $fieldData) {
            if (array_key_exists('dynamicBase', $fieldData)) {
                $originalFieldName = substr($key, 0, - strlen($fieldData->dynamicBase) + 1);

                // Maintain case sensitive variable names
                $listFields [$originalFieldName] = $key;
            } else {
                // Maintain case sensitive variable names
                $listFields [$key] = $key;
            }
        }

        return $listFields;
    }
}
