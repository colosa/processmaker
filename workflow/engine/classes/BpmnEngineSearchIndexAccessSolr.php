<?php

use ProcessMaker\Core\System;

/**
 * Interface to the Solr Search server
 */
class BpmnEngineSearchIndexAccessSolr
{
    const SOLR_VERSION = '&version=2.2';

    private $_solrIsEnabled = false;
    private $_solrHost = "";

    public function __construct($solrIsEnabled = false, $solrHost = "")
    {
        // use the parameters to initialize class
        $this->_solrIsEnabled = $solrIsEnabled;
        $this->_solrHost = $solrHost;
    }

    /**
     * Verify if the Solr service is available
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return bool
     */
    public function isEnabled($workspace)
    {
        $resultServerStatus = false;
        // verify solr server response
        try {
            $resultServerStatus = $this->ping($workspace);
        } catch (Exception $ex) {
            $resultServerStatus = false;
        }

        return $resultServerStatus;
    }

    /**
     * Returns the total number of indexed documents
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @param
     *          workspace: workspace name
     * @return total
     */
    public function getNumberDocuments($workspace)
    {
        // get total number of documents in registry
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/select/?q=*:*";
        $solrIntruct .= self::SOLR_VERSION;
        $solrIntruct .= "&start=0&rows=0&echoParams=none&wt=json";

        $handlerTotal = curl_init($solrIntruct);
        curl_setopt($handlerTotal, CURLOPT_RETURNTRANSFER, true);

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handlerTotal, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handlerTotal, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handlerTotal, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handlerTotal, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $responseTotal = curl_exec($handlerTotal);
        curl_close($handlerTotal);

        // verify the result of solr
        $responseSolrTotal = G::json_decode($responseTotal);
        if ($responseSolrTotal->responseHeader->status != 0) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error returning the total number of documents in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
        $numTotalDocs = $responseSolrTotal->response->numFound;
        return $numTotalDocs;
    }

    /**
     * Execute a query in base to Requested data
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function executeQuery($solrRequestData)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $workspace = $solrRequestData->workspace;

        // format request
        $query = empty($solrRequestData->searchText) ? '*:*' : $solrRequestData->searchText;
        $query = rawurlencode($query);
        $start = '&start=' . $solrRequestData->startAfter;
        $rows = '&rows=' . $solrRequestData->pageSize;
        $fieldList = '';
        $cols = $solrRequestData->includeCols;
        if (!empty($cols)) {
            $fieldList = "&fl=" . implode(",", $cols);
        }
        $sort = '';
        if ($solrRequestData->numSortingCols > 0) {
            $sort = '&sort=';
            for ($i = 0; $i < $solrRequestData->numSortingCols; $i++) {
                $sort .= $solrRequestData->sortCols [$i] . "%20" . $solrRequestData->sortDir [$i] . ",";
            }

            $sort = substr_replace($sort, "", -1);
        }
        $resultFormat = empty($solrRequestData->resultFormat) ? '' : '&wt=' . $solrRequestData->resultFormat;
        $filters = '';
        $aFilters = explode(',', $solrRequestData->filterText);
        foreach ($aFilters as $value) {
            $filters .= '&fq=' . urlencode($value);
        }

        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/select/?q=$query";
        $solrIntruct .= "&echoParams=none";
        $solrIntruct .= self::SOLR_VERSION;
        $solrIntruct .= $start;
        $solrIntruct .= $rows;
        $solrIntruct .= $fieldList;
        $solrIntruct .= $sort;
        $solrIntruct .= $filters;
        $solrIntruct .= $resultFormat;
        // send query
        // search the cases in base to datatable parameters
        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        // decode
        $responseSolr = G::json_decode($response);
        if ($responseSolr->responseHeader->status != 0) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error executing query to Solr." . $solrIntruct . " response error: " . $response . "\n");
        }

        return $responseSolr;
    }

    /**
     * Insert or Update document index
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function updateDocument($solrUpdateDocument)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $solrUpdateDocument->workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, $solrUpdateDocument->document); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error updating document in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Commit the changes since the last commit
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function commitChanges($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, "<commit/>"); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error commiting changes in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Rollback the changes since the last commit
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function rollbackChanges($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, "<rollback/>"); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error rolling back changes in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Optimize Solr index
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function optimizeChanges($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, "<optimize/>"); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error optimizing changes in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Return the list of the stored fields in Solr
     *
     * @param string $workspace
     *          Solr instance name
     * @throws Exception
     * @return void mixed of field names
     */
    public function getListIndexedStoredFields($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/admin/luke?numTerms=0&wt=json";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);
        // decode
        $responseSolr = G::json_decode($response);
        if ($responseSolr->responseHeader->status != 0) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error getting index fields in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
        return $responseSolr;
    }

    /**
     * Ping the Solr Server to check his health
     *
     * @param string $workspace
     *          Solr instance name
     * @throws Exception
     * @return void mixed of field names
     */
    public function ping($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/admin/ping?wt=json";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
        curl_close($handler);

        //there's no response
        if (!$response) {
            return false;
        }

        // decode
        $responseSolr = G::json_decode($response);
        if ($responseSolr->responseHeader->status != "OK") {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error pinging Solr server." . $solrIntruct . " response error: " . $response . "\n");
        }
        return true;
    }

    /**
     * Delete all documents from index
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function deleteAllDocuments($workspace)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, "<delete><query>*:*</query></delete>"); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);

        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error deleting all documents in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Delete specified documents from index
     * @gearman = false
     * @rest = false
     * @background = false
     *
     * @return solr response
     */
    public function deleteDocument($workspace, $idQuery)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/update";

        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array(
            'Content-type:application/xml'
        ));
        curl_setopt($handler, CURLOPT_BINARYTRANSFER, true); // --data-binary
        curl_setopt($handler, CURLOPT_POSTFIELDS, "<delete><query>" . $idQuery . "</query></delete>"); // data
        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);

        curl_close($handler);

        $swOk = strpos($response, '<int name="status">0</int>');
        if (!$swOk) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error deleting document in Solr." . $solrIntruct . " response error: " . $response . "\n");
        }
    }

    /**
     * Execute a query in base to Request data
     *
     * @param EntityFacetRequest $facetRequestEntity
     * @return solr response: list of facets array
     */
    public function getFacetsList($facetRequest)
    {
        $solrIntruct = '';
        // get configuration information in base to workspace parameter
        $workspace = $facetRequest->workspace;

        // format request
        $query = empty($facetRequest->searchText) ? '*:*' : $facetRequest->searchText;
        $query = rawurlencode($query);
        $start = '&start=0';
        $rows = '&rows=0';
        $facets = '&facet=on&facet.mincount=1&facet.limit=20'; // enable facet and
        // only return facets
        // with minimun one
        // instance
        foreach ($facetRequest->facetFields as $value) {
            $facets .= '&facet.field=' . $value;
        }
        foreach ($facetRequest->facetQueries as $value) {
            $facets .= '&facet.query=' . $value;
        }
        if (!empty($facetRequest->facetDates)) {
            foreach ($facetRequest->facetDates as $value) {
                $facets .= '&facet.date=' . $value;
            }
            $facets .= '&facet.date.start=' . $facetRequest->facetDatesStart;
            $facets .= '&facet.date.end=' . $facetRequest->facetDatesEnd;
            $facets .= '&facet.date.gap=' . $facetRequest->facetDateGap;
        }
        $filters = '';
        foreach ($facetRequest->filters as $value) {
            $filters .= '&fq=' . $value;
        }

        $resultFormat = '&wt=json';

        $solrIntruct = (substr($this->_solrHost, -1) == "/") ? $this->_solrHost : $this->_solrHost . "/";
        $solrIntruct .= $workspace;
        $solrIntruct .= "/select/?q=$query";
        $solrIntruct .= "&echoParams=none";
        $solrIntruct .= self::SOLR_VERSION;
        $solrIntruct .= $start;
        $solrIntruct .= $rows;
        $solrIntruct .= $facets;
        $solrIntruct .= $filters;
        $solrIntruct .= $resultFormat;

        // send query
        // search the cases in base to datatable parameters
        $handler = curl_init($solrIntruct);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt($handler, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : ''));
            if ($sysConf['proxy_port'] != '') {
                curl_setopt($handler, CURLOPT_PROXYPORT, $sysConf['proxy_port']);
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt($handler, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : ''));
            }
            curl_setopt($handler, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        $response = curl_exec($handler);
        curl_close($handler);

        // decode
        $responseSolr = G::json_decode($response);
        if ($responseSolr->responseHeader->status != 0) {
            throw new Exception(date('Y-m-d H:i:s:u') . " Error getting faceted list from Solr." . $solrIntruct . " response error: " . $response . "\n");
        }

        return $responseSolr;
    }
}
