<?php

/**
 * Interface to the Solr Search server 
 * @author Herbert Saal Gutierrez
 *
 */
class BpmnEngine_SearchIndexAccess_Solr {
  const SOLR_VERSION = '&version=2.2';
  private $solrIsEnabled = false;
  private $solrHost = "";
  
  function __construct($solrIsEnabled = false, $solrHost = "") {
    // use the parameters to initialize class
    $this->solrIsEnabled = $solrIsEnabled;
    $this->solrHost = $solrHost;
  }
  
  /**
   * Verify if the Solr service is available
   * @gearman = false
   * @rest = false
   * @background = false
   * 
   * @return bool
   */
  function isEnabled() {
    // verify solr server response
    
    return $this->solrIsEnabled;
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
  function getNumberDocuments($workspace) {
    if (! $this->solrIsEnabled)
      return;
      // get configuration information in base to workspace parameter
      
    // get total number of documents in registry
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/select/?q=*:*";
    $solrIntruct .= self::SOLR_VERSION;
    $solrIntruct .= "&start=0&rows=0&echoParams=none&wt=json";
    
    $handlerTotal = curl_init ( $solrIntruct );
    curl_setopt ( $handlerTotal, CURLOPT_RETURNTRANSFER, true );
    $responseTotal = curl_exec ( $handlerTotal );
    curl_close ( $handlerTotal );
    
    // verify the result of solr
    $responseSolrTotal = json_decode ( $responseTotal, true );
    if ($responseSolrTotal['responseHeader']['status'] != 0) {
      throw new Exception ( "Error returning the total number of documents in Solr." . $solrIntruct);
    }
    $numTotalDocs = $responseSolrTotal ['response'] ['numFound'];
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
  function executeQuery($solrRequestData) {
    if (! $this->solrIsEnabled)
      return;
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $workspace = $solrRequestData->workspace;
    
    // format request
    $query = empty ( $solrRequestData->searchText ) ? '*:*' : $solrRequestData->searchText;
    $query = rawurlencode ( $query );
    $start = '&start=' . $solrRequestData->startAfter;
    $rows = '&rows=' . $solrRequestData->pageSize;
    $fieldList = '';
    $cols = $solrRequestData->includeCols;
    if (! empty ( $cols )) {
      $fieldList = "&fl=" . implode ( ",", $cols );
    }
    $sort = '';
    if ($solrRequestData->numSortingCols > 0) {
      $sort = '&sort=';
      for($i = 0; $i < $solrRequestData->numSortingCols; $i ++) {
        $sort .= $solrRequestData->sortCols [$i] . "%20" . $solrRequestData->sortDir [$i] . ",";
      }
      
      $sort = substr_replace ( $sort, "", - 1 );
    }
    $resultFormat = empty ( $solrRequestData->resultFormat ) ? '' : '&wt=' . $solrRequestData->resultFormat;
    $filters = '';
    $aFilters = explode ( ',', $solrRequestData->filterText );
    foreach ( $aFilters as $value ) {
      $filters .= '&fq=' . urlencode ( $value );
    }
    
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
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
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    $response = curl_exec ( $handler );
    curl_close ( $handler );

    // decode
    $responseSolr = json_decode ( $response, true );
    if ($responseSolr['responseHeader']['status'] != 0) {
      throw new Exception ( "Error executing query to Solr." . $solrIntruct);
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
  function updateDocument($solrUpdateDocument) {
    if (! $this->solrIsEnabled)
      return;
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $solrUpdateDocument->workspace;
    $solrIntruct .= "/update";

    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, $solrUpdateDocument->document ); // data
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error updating document in Solr." . $solrIntruct);
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
  function commitChanges($workspace) {
    if (! $this->solrIsEnabled)
      return;
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/update";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, "<commit/>" ); // data
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error commiting changes in Solr." . $solrIntruct);
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
  function rollbackChanges($workspace) {
    if (! $this->solrIsEnabled)
      return;
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/update";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, "<rollback/>" ); // data
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error rolling back changes in Solr." . $solrIntruct);
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
  function optimizeChanges($workspace) {
    if (! $this->solrIsEnabled)
      return;
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/update";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, "<optimize/>" ); // data
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error optimizing changes in Solr." . $solrIntruct);
    }
  }
  
  /**
   * Return the list of the stored fields in Solr 
   * @param string $workspace Solr instance name
   * @throws Exception
   * @return void|mixed array of field names
   */
  function getListIndexedStoredFields($workspace) {
    if (! $this->solrIsEnabled)
      return;
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/admin/luke?numTerms=0&wt=json";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    // decode
    $responseSolr = json_decode ( $response, true );
    if ($responseSolr['responseHeader']['status'] != 0) {
      throw new Exception ( "Error getting index fields in Solr." . $solrIntruct);
    }
    return $responseSolr;
  }
  
  /**
   * Delete all documents from index
   * @gearman = false
   * @rest = false
   * @background = false
   * 
   * @return solr response
   */
  function deleteAllDocuments($workspace) {
    if (! $this->solrIsEnabled)
      return;
      // $registry = Zend_Registry::getInstance();
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/update";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, "<delete><query>*:*</query></delete>" ); // data
    $response = curl_exec ( $handler );
    
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error deleting all documents in Solr." . $solrIntruct);
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
  function deleteDocument($workspace, $idQuery) {
    if (! $this->solrIsEnabled)
      return;
      // $registry = Zend_Registry::getInstance();
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
    $solrIntruct .= $workspace;
    $solrIntruct .= "/update";
    
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $handler, CURLOPT_HTTPHEADER, array (
        'Content-type:application/xml' 
    ) ); // -H
    curl_setopt ( $handler, CURLOPT_BINARYTRANSFER, TRUE ); // --data-binary
    curl_setopt ( $handler, CURLOPT_POSTFIELDS, "<delete><query>" . $idQuery . "</query></delete>" ); // data
    $response = curl_exec ( $handler );
    
    curl_close ( $handler );
    
    $swOk = strpos ( $response, '<int name="status">0</int>' );
    if (! $swOk) {
      throw new Exception ( "Error deleting document in Solr." . $solrIntruct);
    }
  }
  
  /**
   * Execute a query in base to Request data
   * 
   * @param Entity_FacetRequest $facetRequestEntity          
   * @return solr response: list of facets array
   */
  function getFacetsList($facetRequest) {
    if (! $this->solrIsEnabled)
      return;
    
    $solrIntruct = '';
    // get configuration information in base to workspace parameter
    $workspace = $facetRequest->workspace;
    
    // format request
    $query = empty ( $facetRequest->searchText ) ? '*:*' : $facetRequest->searchText;
    $query = rawurlencode ( $query );
    $start = '&start=0';
    $rows = '&rows=0';
    $facets = '&facet=on&facet.mincount=1&facet.limit=20'; // enable facet and
                                                           // only return facets
                                                           // with minimun one
                                                           // instance
    foreach ( $facetRequest->facetFields as $value ) {
      $facets .= '&facet.field=' . $value;
    }
    foreach ( $facetRequest->facetQueries as $value ) {
      $facets .= '&facet.query=' . $value;
    }
    if (! empty ( $facetRequest->facetDates )) {
      foreach ( $facetRequest->facetDates as $value ) {
        $facets .= '&facet.date=' . $value;
      }
      $facets .= '&facet.date.start=' . $facetRequest->facetDatesStart;
      $facets .= '&facet.date.end=' . $facetRequest->facetDatesEnd;
      $facets .= '&facet.date.gap=' . $facetRequest->facetDateGap;
    }
    $filters = '';
    foreach ( $facetRequest->filters as $value ) {
      $filters .= '&fq=' . $value;
    }
    // echo "<pre>";
    
    $resultFormat = '&wt=json';
    
    $solrIntruct = (substr($this->solrHost, -1) == "/")?$this->solrHost:$this->solrHost . "/";
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
    $handler = curl_init ( $solrIntruct );
    curl_setopt ( $handler, CURLOPT_RETURNTRANSFER, true );
    $response = curl_exec ( $handler );
    curl_close ( $handler );
    
    // decode
    $responseSolr = json_decode ( $response, true );
    if ($responseSolr['responseHeader']['status'] != 0) {
      throw new Exception ( "Error getting faceted list from Solr." . $solrIntruct);
    }
    
    return $responseSolr;
  }
}