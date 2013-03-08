<?php

$request = isset($_POST['request'])? $_POST['request']: null;
if( !isset($request) ){
  $request = isset($_GET['request'])? $_GET['request']: null;
}
if( isset($request) ){
  switch($request){
    case 'deleteGridRowOnDynaform':
      //This code is to update the SESSION variable for dependent fields in grids

      if (!defined("XMLFORM_AJAX_PATH")) {
          define("XMLFORM_AJAX_PATH", PATH_XMLFORM);
      }

      if (is_array($_SESSION[$_POST["formID"]][$_POST["gridname"]])) {
          if (!is_array($_SESSION[$_POST["formID"]][$_POST["gridname"]])) {
              $_SESSION[$_POST["formID"]][$_POST["gridname"]] = (array)$_SESSION[$_POST["formID"]][$_POST["gridname"]];
          }
          ksort($_SESSION[$_POST["formID"]][$_POST["gridname"]]);
          $oFields = array();
          $initialKey = 1;

          foreach ($_SESSION[$_POST["formID"]][$_POST["gridname"]] as $key => $value) {
              if ($key != $_POST["rowpos"]) {
                  $oFields[$initialKey] = $value;
                  $initialKey++;
              }
          }

          unset($_SESSION[$_POST["formID"]][$_POST["gridname"]]);

          $_SESSION[$_POST["formID"]][$_POST["gridname"]] = $oFields;
      }

    /*  if( isset($_SESSION['APPLICATION']) ){
        G::LoadClass('case');
        $oApp= new Cases();
        $aFields = $oApp->loadCase($_SESSION['APPLICATION']);
        unset($aFields['APP_DATA'][$_POST['gridname']][$_POST['rowpos']]);
        $initialKey = 1;
        foreach ($aFields['APP_DATA'][$_POST['gridname']] as $key => $value) {
          $oFields[$initialKey] = $value;
          $initialKey++;
          $aFields['APP_DATA'][$_POST['gridname']] = $oFields;
        }
        $oApp->updateCase($_SESSION['APPLICATION'], $aFields);
      }
*/
    break;
    /** widgets **/
    case 'suggest':

      try {
        if(isset($_GET["inputEnconde64"])) {
          $_GET['input'] = base64_decode($_GET['input']);
        }
        $sData = base64_decode(str_rot13($_GET['hash']));
        list($SQL, $DB_UID) = explode('@|', $sData);
        // Remplace values for dependent fields
        $aDependentFieldsKeys  = explode("|", base64_decode(str_rot13($_GET['dependentFieldsKeys'])));
        $aDependentFieldsValue = explode("|", $_GET['dependentFieldsValue']);
        if($aDependentFieldsKeys){
          $SQL = str_replace($aDependentFieldsKeys, $aDependentFieldsValue, $SQL);
        }

        // Parsed SQL Structure
        G::LoadClass('phpSqlParser');

        $parser = new PHPSQLParser($SQL);
        // Verif parsed array
        // print_r($parser->parsed);
        $SQL = queryModified($parser->parsed, $_GET['input']);

        $aRows = Array();
        try {
          $con = Propel::getConnection($DB_UID);
          $con->begin();
          $rs = $con->executeQuery($SQL);
          $con->commit();

          while ( $rs->next() ) {
            array_push($aRows, $rs->getRow());
          }
        } catch (SQLException $sqle) {
            $con->rollback();
        }

        $input    = strtolower( $_GET['input'] );
        $len      = strlen($input);
        $limit    = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
        $aResults = array();
        $count    = 0;
        $aRows    = sortByChar($aRows, $input);

        if ($len){
          for ($i=0;$i<count($aRows);$i++){
            $aRow  = $aRows[$i];
            $nCols = sizeof($aRow);

            $aRow  = array_values($aRow);
            switch( $nCols ){
              case 1:
                  $id    = $aRow[0];
                  $value = $aRow[0];
                  $info  = '';
                  break;

              case 2:
                  $id    = $aRow[0];
                  $value = $aRow[1];
                  $info  = '';
                  break;

              case $nCols >= 3:
                  $id    = $aRow[0];
                  $value = $aRow[1];
                  $info  = $aRow[2];
                  break;
            }


            // had to use utf_decode, here
            // not necessary if the results are coming from mysql
            //
            $count++;
            $aResults[] = array( "id"=>$id ,"value"=>htmlspecialchars($value), "info"=>htmlspecialchars($info) );

          }
        }

        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header ("Pragma: no-cache"); // HTTP/1.0

        if (isset($_REQUEST['json'])) {
            header("Content-Type: application/json");
            echo json_encode(array("status" => 0, "results" => $aResults));
         } else {
          header("Content-Type: text/xml");

          echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
          for ($i=0;$i<count($aResults);$i++)
          {
            echo "<rs id=\"".$aResults[$i]['id']."\" info=\"".$aResults[$i]['info']."\">".$aResults[$i]['value']."</rs>";
          }
          echo "</results>";
        }

      } catch(Exception $e){
        $err = $e->getMessage();
        //$err = eregi_replace("[\n|\r|\n\r]", ' ', $err);
        $err = preg_replace("[\n|\r|\n\r]", ' ', $err);//Made compatible to PHP 5.3
        echo '{"status":1, "message":"'.$err.'"}';
      }
    break;


    case 'storeInTmp':
      try {
        $con = Propel::getConnection($_GET['cnn']);
        if($_GET['pkt'] == 'int'){
          $rs = $con->executeQuery("SELECT MAX({$_GET['pk']}) as lastId FROM {$_GET['table']};");
          $rs->next();
          $row = $rs->getRow();
          $gKey = (int)$row['lastId'] + 1;

        } else {
          $gKey = md5(date('Y-m-d H:i:s').'@'.rand());
        }

        $rs = $con->executeQuery("INSERT INTO {$_GET['table']} ({$_GET['pk']}, {$_GET['fld']}) VALUES ('$gKey', '{$_GET['value']}');");

        echo "{status: 1, message: \"success\"}";
      } catch (Exception $e) {
        $err = $e->getMessage();
        //$err = eregi_replace("[\n|\r|\n\r]", ' ', $err);
        $err = preg_replace("[\n|\r|\n\r]", " ", $err); //Made compatible to PHP 5.3

        echo "{status: 0, message: \"" . $err . "\"}";
      }
      break;
  }
}

function sortByChar($aRows, $charSel)
{
  $aIniChar = array();
  $aRest    = array();
  for($i=0; $i<count($aRows) ;$i++){
    $aRow      = $aRows[$i];
    $nCols     = sizeof($aRow);
    $aRowOrder = array_values($aRow);
    switch( $nCols ){
        case 1:
            $value = $aRowOrder[0];
            break;
        case 2:
            $value = $aRowOrder[1];
            break;
        case $nCols >= 3:
            $value = $aRowOrder[1];
            break;
    }

    if(substr(strtolower($value), 0, 1) == substr( strtolower($charSel), 0, 1)){
      array_push($aIniChar, $aRow);
    } else {
      array_push($aRest, $aRow);
    }
  }
  return array_merge($aIniChar, $aRest);
}


/*
 * Converts a SQL array parsing to a SQL string.
 * @param string $sqlParsed
 * @param string $inputSel default value empty string
 * @return string
 */
function queryModified($sqlParsed, $inputSel = "")
{

  if(!empty($sqlParsed['SELECT'])) {

    $sqlSelect = "SELECT ";
    $aSelect   = $sqlParsed['SELECT'];

    $sFieldSel = (count($aSelect)>1 ) ? $aSelect[1]['base_expr'] : $aSelect[0]['base_expr'];
    foreach($aSelect as $key => $value ) {
      if($key != 0)
        $sqlSelect .= ", ";
      $sAlias    = str_replace("`","", $aSelect[$key]['alias']);
      $sBaseExpr = $aSelect[$key]['base_expr'];
      switch($aSelect[$key]['expr_type']){
        case 'colref' : if($sAlias === $sBaseExpr)
                           $sqlSelect .= $sAlias;
                         else
                           $sqlSelect .= $sBaseExpr . ' AS ' . $sAlias;
                         break;
        case 'expression' : if($sAlias === $sBaseExpr)
                           $sqlSelect .= $sBaseExpr;
                         else
                           $sqlSelect .= $sBaseExpr . ' AS ' . $sAlias;
                         break;
        case 'subquery' : if(strpos($sAlias, $sBaseExpr,0) != 0)
                           $sqlSelect .= $sAlias;
                         else
                           $sqlSelect .= $sBaseExpr . " AS " . $sAlias;
                         break;
        case 'operator' : $sqlSelect .= $sBaseExpr;
                         break;
        default        : $sqlSelect .= $sBaseExpr;
                         break;
      }
    }

    $sqlFrom = " FROM ";
    if(!empty($sqlParsed['FROM'])){
      $aFrom = $sqlParsed['FROM'];
      if(count($aFrom) > 0){
        foreach($aFrom as $key => $value ){
          if($key == 0) {
            $sqlFrom .= $aFrom[$key]['table'] . (($aFrom[$key]['table'] == $aFrom[$key]['alias'])?"" : " " . $aFrom[$key]['alias']);
          } else {
            $sqlFrom .= " " . (($aFrom[$key]['join_type']=='JOIN')?"INNER": $aFrom[$key]['join_type']) . " JOIN " . $aFrom[$key]['table']
                     . (($aFrom[$key]['table'] == $aFrom[$key]['alias'])?"" : " " . $aFrom[$key]['alias']) . " " . $aFrom[$key]['ref_type'] . " " . $aFrom[$key]['ref_clause'] ;
          }

        }
      }
    }

    if(!empty($sqlParsed['WHERE'])){
      $sqlWhere = " WHERE ";
      $aWhere   = $sqlParsed['WHERE'];
      foreach($aWhere as $key => $value ){
        $sqlWhere .= $value['base_expr'] . " ";
      }
      $sqlWhere .= " AND " . $sFieldSel . " LIKE '%". $inputSel . "%'";
    }
    else {
      $sqlWhere = " WHERE " . $sFieldSel . " LIKE '%". $inputSel ."%' ";
    }

    $sqlGroupBy = "";
    if(!empty($sqlParsed['GROUP'])){
      $sqlGroupBy = "GROUP BY ";
      $aGroup     = $sqlParsed['GROUP'];
      foreach($aGroup as $key => $value ){
        if($key != 0)
          $sqlGroupBy .= ", ";
        if($value['direction'] == 'ASC' )
          $sqlGroupBy .= $value['base_expr'];
        else
          $sqlGroupBy .= $value['base_expr'] . " " . $value['direction'];
      }
    }

    $sqlHaving = "";
    if(!empty($sqlParsed['HAVING'])){
      $sqlHaving = "HAVING ";
      $aHaving   = $sqlParsed['HAVING'];
      foreach($aHaving as $key => $value ){
        $sqlHaving .= $value['base_expr'] . " ";
      }
    }

    $sqlOrderBy = "";
    if(!empty($sqlParsed['ORDER'])){
      $sqlOrderBy = "ORDER BY ";
      $aOrder     = $sqlParsed['ORDER'];
      foreach($aOrder as $key => $value ){
        if($key != 0)
          $sqlOrderBy .= ", ";
        if($value['direction'] == 'ASC' )
          $sqlOrderBy .= $value['base_expr'];
        else
          $sqlOrderBy .= $value['base_expr'] . " " . $value['direction'];
      }
    } else {
      $sqlOrderBy = " ORDER BY " . $sFieldSel;
    }

    $sqlLimit  = "";
    if(!empty($sqlParsed['LIMIT'])){
      $sqlLimit  = "LIMIT ". $sqlParsed['LIMIT']['start'] . ", " . $sqlParsed['LIMIT']['end'];
    }

    return $sqlSelect . $sqlFrom . $sqlWhere . $sqlGroupBy . $sqlHaving . $sqlOrderBy . $sqlLimit;
  }
  if(!empty($sqlParsed['CALL'])){
    $sCall = "CALL ";
    $aCall = $sqlParsed['CALL'];
    foreach($aCall as $key => $value ){
      $sCall .= $value . " ";
    }
    return $sCall;
  }
  if(!empty($sqlParsed['EXECUTE'])){
    $sCall = "EXECUTE ";
    $aCall = $sqlParsed['EXECUTE'];
    foreach($aCall as $key => $value ){
      $sCall .= $value . " ";
    }
    return $sCall;
  }
  if(!empty($sqlParsed[''])){
    $sCall = "";
    $aCall = $sqlParsed[''];
    foreach($aCall as $key => $value ){
      $sCall .= $value . " ";
    }
    return $sCall;
  }
}
