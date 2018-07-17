<?php
$filter = new InputFilter();
$_GET = $filter->xssFilterHard($_GET, "url");
$_POST = $filter->xssFilterHard($_POST, "url");
$_REQUEST = $filter->xssFilterHard($_REQUEST, "url");
$_SESSION = $filter->xssFilterHard($_SESSION, "url");

$request = isset($_POST['request']) ? $_POST['request'] : null;

if (!isset($request)) {
    $request = isset($_GET['request']) ? $_GET['request'] : null;
}
if (isset($request)) {
    switch ($request) {
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

            break;
        /** widgets **/
        case 'suggest':

            try {

                if (isset($_GET["inputEnconde64"])) {
                    $_GET['input'] = base64_decode($_GET['input']);
                }

                if (!isset($_GET['form']) || !isset($_GET['variable'])) {
                    throw new Exception('Please contact the system administrator.');
                }

                $gridName = isset($_GET['grid']) ? $_GET['grid'] : '';
                //When is a grid the form parameter include the name of grid
                $xmlFile = str_replace($gridName, '', $_GET['form']);
                //We will to get the form and variable and the query related
                $xmlFile = G::getUIDName(urlDecode($xmlFile));
                $gridName = isset($_GET['grid']) ? $_GET['grid'] : '';
                $xmlFile = str_replace($gridName, '', $xmlFile);

                $myForm = new Form($xmlFile, PATH_DYNAFORM);
                $myForm->id = urlDecode($_GET['form']);


                $bdUid = 'workflow';
                if (isset($_GET['type']) && $_GET['type']==='form' && isset($myForm->fields[$_GET['variable']]->sql)) {
                    $sqlQuery = $myForm->fields[$_GET['variable']]->sql;
                    if (isset($myForm->fields[$_GET['variable']]->sqlConnection) && !empty($myForm->fields[$_GET['variable']]->sqlConnection)) {
                        $bdUid = $myForm->fields[$_GET['variable']]->sqlConnection;
                    }
                } elseif (isset($_GET['type']) && $_GET['type']==='grid' &&  isset($myForm->fields[$_GET['grid']])) {
                    foreach ($myForm->fields[$_GET['grid']] as $index => $value) {
                        if (is_array($value) && isset($value[$_GET['variable']])) {
                            $newObj = $value[$_GET['variable']];
                            $sqlQuery = $newObj->sql;
                            if (isset($newObj->sqlConnection) && !empty($newObj->sqlConnection)) {
                                $bdUid = $newObj->sqlConnection;
                            }
                        }
                    }
                } else {
                    throw new Exception('The variable with ' . $_GET['variable'] . ' does not defined in the form.');
                }

                // Replace values for dependent fields
                $aDependentFieldsKeys = explode("|", base64_decode(str_rot13($_GET['dependentFieldsKeys'])));
                $aDependentFieldsValue = explode("|", $_GET['dependentFieldsValue']);
                if ($aDependentFieldsKeys) {
                    $aDependentFields = array();
                    foreach ($aDependentFieldsKeys as $nKey => $sFieldVar) {
                        $sKeyDepFields = substr($sFieldVar, 2);
                        $aDependentFields[$sKeyDepFields] = $aDependentFieldsValue[$nKey];
                    }
                    $sqlQuery = G::replaceDataField($sqlQuery, $aDependentFields);
                }

                // Parsed SQL Structure

                $parser = new PHPSQLParser($sqlQuery);
                $searchType = $_GET["searchType"];

                // Verify parsed array
                $sqlQuery = queryModified($parser->parsed, $_GET['input'], $searchType);

                $aRows = Array();
                try {
                    $con = Propel::getConnection($bdUid);
                    $con->begin();
                    $rs = $con->executeQuery($sqlQuery);
                    $con->commit();

                    while ($rs->next()) {
                        array_push($aRows, $rs->getRow());
                    }
                } catch (SQLException $sqle) {
                    $con->rollback();
                }

                $input = strtolower($_GET['input']);
                $len = strlen($input);
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
                $aResults = array();
                $count = 0;
                $aRows = sortByChar($aRows, $input);

                if ($len) {
                    for ($i = 0; $i < count($aRows); $i++) {
                        $aRow = $aRows[$i];
                        $nCols = sizeof($aRow);

                        $aRow = array_values($aRow);
                        switch ($nCols) {
                            case 1:
                                $id = $aRow[0];
                                $value = $aRow[0];
                                $info = '';
                                break;

                            case 2:
                                $id = $aRow[0];
                                $value = $aRow[1];
                                $info = '';
                                break;

                            case $nCols >= 3:
                                $id = $aRow[0];
                                $value = $aRow[1];
                                $info = $aRow[2];
                                break;
                        }


                        // had to use utf_decode, here
                        // not necessary if the results are coming from mysql
                        //
                        $count++;
                        $aResults[] = array(
                            "id" => $id,
                            "value" => htmlspecialchars($value),
                            "info" => htmlspecialchars($info)
                        );

                    }
                }

                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
                header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                header("Pragma: no-cache"); // HTTP/1.0

                if (isset($_REQUEST['json'])) {
                    header("Content-Type: application/json");
                    echo Bootstrap::json_encode(array("status" => 0, "results" => $aResults));
                } else {
                    header("Content-Type: text/xml");

                    echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
                    for ($i = 0; $i < count($aResults); $i++) {
                        echo "<rs id=\"" . $aResults[$i]['id'] . "\" info=\"" . $aResults[$i]['info'] . "\">" . $aResults[$i]['value'] . "</rs>";
                    }
                    echo "</results>";
                }

            } catch (Exception $e) {
                $err = $e->getMessage();
                $err = preg_replace("[\n|\r|\n\r]", ' ', $err);//Made compatible to PHP 5.3
                echo '{"status":1, "message":"' . $err . '"}';
            }
            break;


        case 'storeInTmp':
            if (!isset($_SESSION['USER_LOGGED'])) {
                echo "{status: 1, message: \"success\"}";
                break;
            }
            try {
                $con = Propel::getConnection($_GET['cnn']);
                if ($_GET['pkt'] == 'int') {

                    $primaryKeyField = Propel::getDB($_GET['cnn'])->quoteIdentifier($_GET['pk']);
                    $tableName = Propel::getDB($_GET['cnn'])->quoteIdentifier($_GET['table']);
                    $rs = $con->executeQuery("SELECT MAX($primaryKeyField) as lastId FROM $tableName");
                    $rs->next();
                    $row = $rs->getRow();
                    $gKey = (int)$row['lastId'] + 1;

                } else {
                    $gKey = G::encryptOld(date('Y-m-d H:i:s') . '@' . rand());
                }

                // See above. Gross, but it works.
                $field = mysql_real_escape_string($_GET['fld']);
                $field = str_replace("`", "", $field);

                $query = "INSERT INTO $tableName ($primaryKeyField, $field) VALUES (?, ?)"; // '$gKey', '{$_GET['value']}')";

                $rs = $con->prepareStatement($query);
                $rs->set(1, $gKey);
                $rs->set(2, $_GET['value']);
                $rs->executeQuery();

                echo "{status: 1, message: \"success\"}";
            } catch (Exception $e) {
                $err = $e->getMessage();
                $err = preg_replace("[\n|\r|\n\r]", " ", $err); //Made compatible to PHP 5.3
                echo "{status: 0, message: \"" . $err . "\"}";
            }
            break;
    }
}

function sortByChar($aRows, $charSel)
{
    $aIniChar = array();
    $aRest = array();
    for ($i = 0; $i < count($aRows); $i++) {
        $aRow = $aRows[$i];
        $nCols = sizeof($aRow);
        $aRowOrder = array_values($aRow);
        switch ($nCols) {
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

        if (substr(strtolower($value), 0, 1) == substr(strtolower($charSel), 0, 1)) {
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
function queryModified($sqlParsed, $inputSel = "", $searchType)
{
    if (!empty($sqlParsed['SELECT'])) {
        $sqlSelectOptions = (isset($sqlParsed["OPTIONS"]) && count($sqlParsed["OPTIONS"]) > 0) ? implode(" ",
            $sqlParsed["OPTIONS"]) : null;

        $sqlSelect = "SELECT $sqlSelectOptions ";
        $aSelect = $sqlParsed["SELECT"];

        $sFieldSel = (count($aSelect) > 1) ? $aSelect[1]['base_expr'] : $aSelect[0]['base_expr'];
        foreach ($aSelect as $key => $value) {
            if ($key != 0) {
                $sqlSelect .= ", ";
            }
            $sAlias = str_replace("`", "", $aSelect[$key]['alias']);
            $sBaseExpr = $aSelect[$key]['base_expr'];
            switch ($aSelect[$key]['expr_type']) {
                case 'colref' :
                    if ($sAlias === $sBaseExpr) {
                        $sqlSelect .= $sAlias;
                    } else {
                        $sqlSelect .= $sBaseExpr . ' AS ' . $sAlias;
                    }
                    break;
                case 'expression' :
                    if ($sAlias === $sBaseExpr) {
                        $sqlSelect .= $sBaseExpr;
                    } else {
                        $sqlSelect .= $sBaseExpr . ' AS ' . $sAlias;
                    }
                    break;
                case 'subquery' :
                    if (strpos($sAlias, $sBaseExpr, 0) != 0) {
                        $sqlSelect .= $sAlias;
                    } else {
                        $sqlSelect .= $sBaseExpr . " AS " . $sAlias;
                    }
                    break;
                case 'operator' :
                    $sqlSelect .= $sBaseExpr;
                    break;
                default        :
                    $sqlSelect .= $sBaseExpr;
                    break;
            }
        }

        $sqlFrom = " FROM ";
        if (!empty($sqlParsed['FROM'])) {
            $aFrom = $sqlParsed['FROM'];
            if (count($aFrom) > 0) {
                foreach ($aFrom as $key => $value) {
                    if ($key == 0) {
                        $sqlFrom .= $aFrom[$key]['table'] . (($aFrom[$key]['table'] == $aFrom[$key]['alias']) ? "" : " " . $aFrom[$key]['alias']);
                    } else {
                        $sqlFrom .= " " . (($aFrom[$key]['join_type'] == 'JOIN') ? "INNER" : $aFrom[$key]['join_type']) . " JOIN " . $aFrom[$key]['table']
                            . (($aFrom[$key]['table'] == $aFrom[$key]['alias']) ? "" : " " . $aFrom[$key]['alias']) . " " . $aFrom[$key]['ref_type'] . " " . $aFrom[$key]['ref_clause'];
                    }

                }
            }
        }

        $sqlConditionLike = "LIKE '%" . $inputSel . "%'";

        switch ($searchType) {
            case "searchtype*":
                $sqlConditionLike = "LIKE '" . $inputSel . "%'";
                break;
            case "*searchtype":
                $sqlConditionLike = "LIKE '%" . $inputSel . "'";
                break;
        }

        if (!empty($sqlParsed['WHERE'])) {
            $sqlWhere = " WHERE ";
            $aWhere = $sqlParsed['WHERE'];
            foreach ($aWhere as $key => $value) {
                $sqlWhere .= $value['base_expr'] . " ";
            }
            $sqlWhere .= " AND " . $sFieldSel . " " . $sqlConditionLike;
        } else {
            $sqlWhere = " WHERE " . $sFieldSel . " " . $sqlConditionLike;
        }

        $sqlGroupBy = "";
        if (!empty($sqlParsed['GROUP'])) {
            $sqlGroupBy = "GROUP BY ";
            $aGroup = $sqlParsed['GROUP'];
            foreach ($aGroup as $key => $value) {
                if ($key != 0) {
                    $sqlGroupBy .= ", ";
                }
                if ($value['direction'] == 'ASC') {
                    $sqlGroupBy .= $value['base_expr'];
                } else {
                    $sqlGroupBy .= $value['base_expr'] . " " . $value['direction'];
                }
            }
        }

        $sqlHaving = "";
        if (!empty($sqlParsed['HAVING'])) {
            $sqlHaving = "HAVING ";
            $aHaving = $sqlParsed['HAVING'];
            foreach ($aHaving as $key => $value) {
                $sqlHaving .= $value['base_expr'] . " ";
            }
        }

        $sqlOrderBy = "";
        if (!empty($sqlParsed['ORDER'])) {
            $sqlOrderBy = "ORDER BY ";
            $aOrder = $sqlParsed['ORDER'];
            foreach ($aOrder as $key => $value) {
                if ($key != 0) {
                    $sqlOrderBy .= ", ";
                }
                if ($value['direction'] == 'ASC') {
                    $sqlOrderBy .= $value['base_expr'];
                } else {
                    $sqlOrderBy .= $value['base_expr'] . " " . $value['direction'];
                }
            }
        } else {
            $sqlOrderBy = " ORDER BY " . $sFieldSel;
        }

        $sqlLimit = "";
        if (!empty($sqlParsed['LIMIT'])) {
            $sqlLimit = "LIMIT " . $sqlParsed['LIMIT']['start'] . ", " . $sqlParsed['LIMIT']['end'];
        }

        return $sqlSelect . $sqlFrom . $sqlWhere . $sqlGroupBy . $sqlHaving . $sqlOrderBy . $sqlLimit;
    }
    if (!empty($sqlParsed['CALL'])) {
        $sCall = "CALL ";
        $aCall = $sqlParsed['CALL'];
        foreach ($aCall as $key => $value) {
            $sCall .= $value . " ";
        }

        return $sCall;
    }
    if (!empty($sqlParsed['EXECUTE'])) {
        $sCall = "EXECUTE ";
        $aCall = $sqlParsed['EXECUTE'];
        foreach ($aCall as $key => $value) {
            $sCall .= $value . " ";
        }

        return $sCall;
    }
    if (!empty($sqlParsed[''])) {
        $sCall = "";
        $aCall = $sqlParsed[''];
        foreach ($aCall as $key => $value) {
            $sCall .= $value . " ";
        }

        return $sCall;
    }
}
