<?php

// getting the extJs parameters
$callback = isset ( $_POST ['callback'] ) ? $_POST ['callback'] : 'stcCallback1001';
$dir = isset ( $_POST ['dir'] ) ? $_POST ['dir'] : 'DESC';
$sort = isset ( $_POST ['sort'] ) ? $_POST ['sort'] : '';
$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : '0';
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : '25';
$filter = isset ( $_POST ['filter'] ) ? $_POST ['filter'] : '';
$search = isset ( $_POST ['search'] ) ? $_POST ['search'] : '';
$process = isset ( $_POST ['process'] ) ? $_POST ['process'] : '';
$user = isset ( $_POST ['user'] ) ? $_POST ['user'] : '';
$status = isset ( $_POST ['status'] ) ? strtoupper ( $_POST ['status'] ) : '';
$action = isset ( $_GET ['action'] ) ? $_GET ['action'] : (isset ( $_POST ['action'] ) ? $_POST ['action'] : 'todo');
$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : (isset ( $_POST ['type'] ) ? $_POST ['type'] : 'extjs');
$user = isset ( $_POST ['user'] ) ? $_POST ['user'] : '';
$dateFrom = isset ( $_POST ['dateFrom'] ) ? substr ( $_POST ['dateFrom'], 0, 10 ) : '';
$dateTo = isset ( $_POST ['dateTo'] ) ? substr ( $_POST ['dateTo'], 0, 10 ) : '';

try {
  $result = "";

  $userUid = (isset ( $_SESSION ['USER_LOGGED'] ) && $_SESSION ['USER_LOGGED'] != '') ? $_SESSION ['USER_LOGGED'] : null;
  
  if (($action == 'todo' || $action == 'draft' || $action == 'sent' || $action == 'selfservice' || $action == 'unassigned' || $action == 'search') && (($solrConf = System::solrEnv()) !== false)) {
    G::LoadClass ( 'AppSolr' );
    $ApplicationSolrIndex = new AppSolr ($solrConf['solr_enabled'], $solrConf['solr_host'], $solrConf['solr_instance']);
    
    $data = $ApplicationSolrIndex->getAppGridData ( $userUid, $start, $limit, $action, $filter, $search, $process, $user, $status, $type, $dateFrom, $dateTo, $callback, $dir, $sort );
    $result = G::json_encode ( $data );
  }
  else {
    G::LoadClass ( 'applications' );
    $apps = new Applications ();
    $data = $apps->getAll ( $userUid, $start, $limit, $action, $filter, $search, $process, $user, $status, $type, $dateFrom, $dateTo, $callback, $dir, $sort );
    
    $result = G::json_encode ( $data );
  }
  echo $result;
}
catch ( Exception $e ) {
  $msg = array (
      'error' => $e->getMessage () 
  );
  print G::json_encode ( $msg );
}      
