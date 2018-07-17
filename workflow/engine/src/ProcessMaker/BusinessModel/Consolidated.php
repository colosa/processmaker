<?php

namespace ProcessMaker\BusinessModel;

use Cases as classesCases;
use Configurations;
use G;
use Smarty;
use Criteria;
use Exception;
use ListInboxPeer;
use ReportTablePeer;
use ResultSet;
use CaseConsolidatedCorePeer;
use ContentPeer;
use PmDynaform;
use ReportTables;
use TaskPeer;
use XmlForm;
use WsBase;


class Consolidated
{
    /**
     * Get Consolidated
     *
     * @access public
     *
     * @param string $tas_uid , Task Uid
     *
     * @return array
     */
    public function get($tas_uid)
    {
        $criteria = new Criteria();
        $criteria->addSelectColumn(CaseConsolidatedCorePeer::DYN_UID);
        $criteria->addSelectColumn(\ReportTablePeer::REP_TAB_NAME);
        $criteria->addAsColumn('CON_VALUE', \ReportTablePeer::REP_TAB_TITLE);
        $criteria->addSelectColumn(\ReportTablePeer::REP_TAB_UID);
        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->addSelectColumn(CaseConsolidatedCorePeer::CON_STATUS);
        $criteria->addJoin(CaseConsolidatedCorePeer::REP_TAB_UID, ReportTablePeer::REP_TAB_UID, Criteria::LEFT_JOIN);
        $criteria->add(CaseConsolidatedCorePeer::TAS_UID, $tas_uid, Criteria::EQUAL);
        $criteria->add(CaseConsolidatedCorePeer::CON_STATUS, 'ACTIVE', Criteria::EQUAL);

        $dataset = CaseConsolidatedCorePeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        if ($dataset->next()) {
            $response = $dataset->getRow();
        } else {
            $response = array(
                'REP_TAB_UID' => '',
                'REP_TAB_NAME' => '__' . $tas_uid,
                'CON_VALUE' => '__' . $tas_uid,
            );
        }

        return array_change_key_case($response, CASE_LOWER);;
    }

    /**
     * Put Data Generate
     *
     * @access public
     *
     * @param string $app_uid , Process Uid
     * @param string $app_number , Task Uid
     * @param string $del_index , Task Uid
     * @param string $usr_uid , Task Uid
     * @param string $fieldName , Field Name
     * @param string $fieldValue , Field Value
     *
     * @return array
     */
    public function postDerivate($app_uid, $app_number, $del_index, $usr_uid, $fieldName = '', $fieldValue = '')
    {
        $ws = new WsBase();
        $oCase = new ClassesCases();

        if (!isset($Fields["DEL_INIT_DATE"])) {
            $oCase->setDelInitDate($app_uid, $del_index);
            $aFields = $oCase->loadCase($app_uid, $del_index);
            //Update data grid
            $aData = $aFields['APP_DATA'];
            foreach ($aData as $k => $dataField) {
                if (is_array($dataField)) {
                    $pos = count($dataField);
                    if (isset($aData[$k][$pos][$fieldName])) {
                        $aData[$k][$pos][$fieldName] = $fieldValue;
                    }
                }
            }
            $aFields['APP_DATA'] = $aData;
            $oCase->updateCase($app_uid, $aFields);
            //End update
        }

        $res = $ws->derivateCase($usr_uid, $app_uid, $del_index, true);
        $messageDerivateCase = null;

        if (is_array($res)) {
            $messageDerivateCase = "<ul type='square'>";

            if (count($res["routing"]) > 0) {
                foreach ($res["routing"] as $k => $field) {
                    $messageDerivateCase = $messageDerivateCase . "<li>" . $res["routing"][$k]->taskName . " - " . $res["routing"][$k]->userName;
                }
            } else {
                $messageDerivateCase = explode("-", $res["message"]);
                $messageDerivateCase = "<li>" . $messageDerivateCase[0];
            }

            $messageDerivateCase = $messageDerivateCase . "</ul>";
        }

        $response = array();

        $response["casesNumRec"] = self::getCasesNumRec($usr_uid);

        if (is_array($res)) {
            $response ["message"] = "<b>" . G::LoadTranslation("ID_CASE") . " " . $app_number . "</b> " .
                G::LoadTranslation("ID_SUMMARY_DERIVATION_BATCH_ROUTING") . ' :' . " <br> " .
                $messageDerivateCase;
        } else {
            $response ["message"] = G::LoadTranslation("ID_CASE") . " " . $app_number . " " . $res->message;
        }

        return $response;
    }

    /**
     * @param $userUid
     * @return int
     */
    public static function getCasesNumRec($userUid)
    {
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(CaseConsolidatedCorePeer::CON_STATUS);
        $criteria->add(CaseConsolidatedCorePeer::CON_STATUS, "ACTIVE");
        $activeNumRec = CaseConsolidatedCorePeer::doCount($criteria);
        $numRec = 0;

        $criteria->clearSelectColumns();
        $criteria->addAsColumn('NUMREC', 'COUNT(' . ListInboxPeer::TAS_UID . ')');
        $criteria->addJoin(CaseConsolidatedCorePeer::TAS_UID, ListInboxPeer::TAS_UID, Criteria::LEFT_JOIN);
        $criteria->add(ListInboxPeer::USR_UID, $userUid, Criteria::EQUAL);
        $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
        $rsSql = CaseConsolidatedCorePeer::doSelectRS($criteria);
        $rsSql->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsSql->next()) {
            $row = $rsSql->getRow();
            $numRec = $row["NUMREC"];
        }

        $numRec = ($activeNumRec > 0) ? $numRec : 0;

        return $numRec;
    }


    /**
     * Put Data Generate
     *
     * @access public
     *
     * @param string $pro_uid, Process Uid
     * @param string $tas_uid, Task Uid
     * @param string $dyn_uid, DynaForm Uid
     * @param string $usr_uid, User Uid
     * @param array $data
     *
     * @return array
     */
    public function putDataGrid($pro_uid, $tas_uid, $dyn_uid, $usr_uid, $data)
    {
        $option = (isset($data["option"])) ? $data["option"] : null;
        $response = array();
        switch ($option) {
            case "ALL":
                $dataUpdate = $data["dataUpdate"];
                $status = 1;
                try {
                    $array = explode("(sep1 /)", $dataUpdate);
                    for ($i = 0; $i <= count($array) - 1; $i++) {
                        $arrayAux = explode("(sep2 /)", $array[$i]);
                        $data = array(
                            'APP_UID' => $arrayAux[0],
                            $arrayAux[1] => $arrayAux[2]
                        );
                        self::consolidatedUpdate($dyn_uid, $data, $usr_uid);
                    }
                    $response["status"] = "OK";
                } catch (Exception $e) {
                    $response["message"] = $e->getMessage();
                    $status = 0;
                }
                if ($status == 0) {
                    $response["status"] = "ERROR";
                }
                break;
            default:
                $dynUid = $dyn_uid;
                $data = $data['data'];

                $status = 1;

                try {
                    self::consolidatedUpdate($dynUid, $data, $usr_uid);
                    $response["status"] = "OK";
                    $response["success"] = true;
                } catch (Exception $e) {
                    $response["message"] = $e->getMessage();
                    $status = 0;
                }

                if ($status == 0) {
                    $response["status"] = "ERROR";
                    $response["success"] = false;
                }
                break;
        }

        return $response;
    }

    /**
     * Get Data Generate
     *
     * @access public
     * @param string $pro_uid, Process Uid
     * @param string $tas_uid, Task Uid
     * @param string $dyn_uid, DynaForm Uid
     * @param string $usr_uid, User Uid
     * @param string $start
     * @param string $limit
     * @param string $search
     *
     * @return void
     * 
     * @throws Exception
     */
    public function getDataGrid($pro_uid, $tas_uid, $dyn_uid, $usr_uid, $start = '', $limit = '', $search = '')
    {
        $start = !empty($start) ? $start : "0";
        $limit = !empty($limit) ? $limit : "20";
        $search = !empty($search) ? $search : "";

        $callback = isset($_REQUEST["callback"]) ? $_REQUEST["callback"] : "stcCallback1001";
        $dir = isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : "DESC";
        $sort = isset($_REQUEST["sort"]) ? $_REQUEST["sort"] : "";
        $filter = isset($_REQUEST["filter"]) ? $_REQUEST["filter"] : "";
        $user = isset($_REQUEST["user"]) ? $_REQUEST["user"] : "";
        $status = isset($_REQUEST["status"]) ? strtoupper($_REQUEST["status"]) : "";
        $action = isset($_GET["action"]) ? $_GET["action"] : (isset($_REQUEST["action"]) ? $_REQUEST["action"] : "todo");
        $type = isset($_GET["type"]) ? $_GET["type"] : (isset($_REQUEST["type"]) ? $_REQUEST["type"] : "extjs");
        $user = isset($_REQUEST["user"]) ? $_REQUEST["user"] : "";
        $dateFrom = isset($_REQUEST["dateFrom"]) ? substr($_REQUEST["dateFrom"], 0, 10) : "";
        $dateTo = isset($_REQUEST["dateTo"]) ? substr($_REQUEST["dateTo"], 0, 10) : "";

        $rowUid = isset($_REQUEST["rowUid"]) ? $_REQUEST["rowUid"] : "";
        $dropdownList = isset($_REQUEST ["dropList"]) ? G::json_decode($_REQUEST ["dropList"]) : array();

        try {

            $response = array();
            $searchFields = array();

            $criteria = new Criteria();
            $criteria->addSelectColumn(CaseConsolidatedCorePeer::REP_TAB_UID);
            $criteria->add(CaseConsolidatedCorePeer::TAS_UID, $tas_uid, Criteria::EQUAL);
            $caseConsolidated = CaseConsolidatedCorePeer::doSelectRS($criteria);
            $caseConsolidated->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $tableUid = null;
            $tableName = null;

            foreach ($caseConsolidated as $item) {
                $criteria = new Criteria();
                $criteria->addSelectColumn(ReportTablePeer::REP_TAB_NAME);
                $criteria->add(ReportTablePeer::REP_TAB_UID, $item["REP_TAB_UID"]);

                $result = ReportTablePeer::doSelectRS($criteria);
                $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                if ($result->next()) {
                    $row = $result->getRow();

                    $tableUid = $item["REP_TAB_UID"];
                    $tableName = $row["REP_TAB_NAME"];
                } else {
                    throw new Exception("Not found the report table");
                }
            }

            $className = $tableName;

            if (!class_exists($className)) {
                require_once(PATH_DB . SYS_SYS . PATH_SEP . "classes" . PATH_SEP . $className . ".php");
            }

            $oCriteria = new Criteria("workflow");

            $oCriteria->addSelectColumn("*");
            $oCriteria->addSelectColumn($tableName . ".APP_UID");

            $oCriteria->addJoin($tableName . ".APP_UID", ListInboxPeer::APP_UID, Criteria::LEFT_JOIN);

            $oCriteria->add(ListInboxPeer::TAS_UID, $tas_uid);
            $oCriteria->add(ListInboxPeer::USR_UID, $usr_uid);
            $oCriteria->add(ListInboxPeer::APP_STATUS, "TO_DO");

            if ($search != "") {
                $filename = $pro_uid . PATH_SEP . $dyn_uid . ".xml";

                if (!class_exists('Smarty')) {
                    require_once(PATH_THIRDPARTY . 'smarty' . PATH_SEP . 'libs' . PATH_SEP . 'Smarty.class.php');
                }
                $G_FORM = new XmlForm();
                $G_FORM->home = PATH_DYNAFORM;
                $G_FORM->parseFile($filename, SYS_LANG, true);

                foreach ($G_FORM->fields as $key => $val) {
                    switch ($val->type) {
                        case "text":
                        case "textarea":
                        case "currency":
                        case "percentage":
                            $searchFields[] = $val->name;
                            $dataType[] = $val->type;
                            break;
                    }
                }

                $oNewCriteria = new Criteria("workflow");
                $oTmpCriteria = null;
                $sw = 0;

                foreach ($searchFields as $index => $value) {
                    $value = strtoupper($value);
                    eval("\$field = " . $tableName . "Peer::" . $value . ";");

                    if ($sw == 0) {
                        if ($dataType[$index] == 'currency' || $dataType[$index] == 'percentage') {
                            if (is_numeric($search) || is_float($search)) {
                                $oTmpCriteria = $oNewCriteria->getNewCriterion($field, $search);
                            }
                        } else {
                            $oTmpCriteria = $oNewCriteria->getNewCriterion($field, "%" . $search . "%", Criteria::LIKE);
                        }
                    } else {
                        if ($dataType[$index] == 'currency' || $dataType[$index] == 'percentage') {
                            if (is_numeric($search) || is_float($search)) {
                                $oTmpCriteria = $oNewCriteria->getNewCriterion($field, $search)->addOr($oTmpCriteria);
                            }
                        } else {
                            $oTmpCriteria = $oNewCriteria->getNewCriterion($field, "%" . $search . "%",
                                Criteria::LIKE)->addOr($oTmpCriteria);
                        }
                    }

                    $sw = 1;
                }

                if ($oTmpCriteria != null) {
                    $oCriteria->add(
                        $oCriteria->getNewCriterion(ListInboxPeer::APP_NUMBER, $search,
                            Criteria::LIKE)->addOr($oTmpCriteria)
                    );
                } else {
                    $oCriteria->add($oCriteria->getNewCriterion(ListInboxPeer::APP_NUMBER, $search, Criteria::LIKE));
                }
            }


            $filter = new \InputFilter();

            if ($sort != "") {
                $reportTable = new ReportTables();
                $arrayReportTableVar = $reportTable->getTableVars($tableUid);
                $tableName = $filter->validateInput($tableName);
                $sort = $filter->validateInput($sort);
                if (in_array($sort, $arrayReportTableVar)) {
                    $sort = strtoupper($sort);
                    eval('$field = ' . $tableName . 'Peer::' . $sort . ';');
                } else {
                    eval('$field = ListInboxPeer::' . $sort . ';');
                }

                if ($dir == "ASC") {
                    $oCriteria->addAscendingOrderByColumn($field);
                } else {
                    $oCriteria->addDescendingOrderByColumn($field);
                }
            } else {
                $oCriteria->addDescendingOrderByColumn(ListInboxPeer::APP_NUMBER);
            }

            $oCriteria->setLimit($limit);
            $oCriteria->setOffset($start);

            $oDataset = ListInboxPeer::doSelectRS($oCriteria);

            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $aTaskConsolidated = array();

            while ($oDataset->next()) {
                $aRow = $oDataset->getRow();

                foreach ($aRow as $datakey => $dataField) {
                    foreach ($dropdownList as $tmpField) {
                        if ($tmpField == $datakey) {
                            $appUid = $aRow["APP_UID"];
                            $fieldVal = $aRow[$tmpField];
                            $aRow[$tmpField] = self::getDropdownLabel($appUid, $pro_uid, $dyn_uid, $tmpField,
                                $fieldVal);
                        }
                    }
                }

                $aTaskConsolidated[] = $aRow;
            }

            foreach ($aTaskConsolidated as $key => $val) {
                foreach ($val as $iKey => $iVal) {
                    if (self::checkValidDate($iVal)) {
                        $iKeyView = str_replace("-", "/", $val[$iKey]);
                        $iKeyView = str_replace("T", " ", $iKeyView);
                        $val[$iKey] = $iKeyView;
                    }
                }
                $response["data"][] = $val;
            }

            $criteria = new Criteria();
            $criteria->addAsColumn('QTY', 'COUNT(' . ListInboxPeer::TAS_UID . ')');
            $criteria->addJoin(CaseConsolidatedCorePeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
            $criteria->addJoin(CaseConsolidatedCorePeer::TAS_UID, ListInboxPeer::TAS_UID, Criteria::LEFT_JOIN);
            $criteria->add(ListInboxPeer::USR_UID, $usr_uid, Criteria::EQUAL);
            $criteria->add(ListInboxPeer::TAS_UID, $tas_uid, Criteria::EQUAL);
            $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
            $count = CaseConsolidatedCorePeer::doSelectRS($criteria);
            $count->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $totalCount = 0;
            foreach ($count as $item) {
                $totalCount = $totalCount + $item["QTY"];
            }

            $response["totalCount"] = $totalCount;
            echo G::json_encode($response);
        } catch (Exception $e) {
            $msg = array("error" => $e->getMessage());
            echo G::json_encode($msg);
        }
    }

    /**
     * Get Data Generate
     *
     * @access public
     *
     * @param string $pro_uid , Process Uid
     * @param string $tas_uid , Task Uid
     * @param string $dyn_uid , Dynaform Uid
     *
     * @return void
     */
    public function getDataGenerate($pro_uid, $tas_uid, $dyn_uid)
    {
        $hasTextArea = false;

        $conf = new Configurations();
        $generalConfCasesList = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "");
        if (isset($generalConfCasesList["casesListDateFormat"]) && !empty($generalConfCasesList["casesListDateFormat"])) {
            $dateFormat = $generalConfCasesList["casesListDateFormat"];
        } else {
            $dateFormat = "Y/m/d";
        }

        $oDyna = new \Dynaform();
        $dataTask = $oDyna->load($dyn_uid);
        if ($dataTask['DYN_VERSION'] > 0) {
            $_SESSION['PROCESS'] = $pro_uid;
            $pmDyna = new PmDynaform(array('APP_DATA' => array(), "CURRENT_DYNAFORM" => $dyn_uid));
            $json = G::json_decode($dataTask["DYN_CONTENT"]);
            $pmDyna->jsonr($json);
            $fieldsDyna = $json->items[0]->items;
            $xmlfrm = new \stdclass();
            $xmlfrm->fields = array();
            foreach ($fieldsDyna as $key => $value) {
                foreach ($value as $val) {
                    if (isset($val->type) && ($val->type == 'text' || $val->type == 'textarea' || $val->type == 'dropdown' || $val->type == 'checkbox' || $val->type == 'datetime')) {
                        $temp = new \stdclass();
                        $temp->type = $val->type;
                        $temp->label = $val->label;
                        $temp->name = $val->name;
                        $temp->required = (isset($val->required)) ? $val->required : 0;
                        $temp->mode = (isset($val->mode)) ? $val->mode : 'edit';

                        if ((isset($val->options) && !empty($val->options)) || (isset($val->optionsSql) && !empty($val->optionsSql))) {
                            $temp->storeData = '[';

                            if (isset($val->options) && !empty($val->options)) {
                                foreach ($val->options as $valueOption) {
                                    if (isset($valueOption->value)) {
                                        $temp->storeData .= '["' . $valueOption->value . '", "' . $valueOption->label . '"],';
                                    } else {
                                        $temp->storeData .= '["' . $valueOption['value'] . '", "' . $valueOption['label'] . '"],';
                                    }
                                }
                            }

                            if (isset($val->optionsSql) && !empty($val->optionsSql)) {
                                foreach ($val->optionsSql as $valueOption) {
                                    if (isset($valueOption->value)) {
                                        $temp->storeData .= '["' . $valueOption->value . '", "' . $valueOption->label . '"],';
                                    } else {
                                        $temp->storeData .= '["' . $valueOption['value'] . '", "' . $valueOption['label'] . '"],';
                                    }
                                }
                            }

                            $temp->storeData = substr($temp->storeData, 0, -1);
                            $temp->storeData .= ']';
                        }

                        $temp->readOnly = ($temp->mode == 'view') ? "1" : "0";
                        $temp->colWidth = 200;
                        $xmlfrm->fields[] = $temp;
                    }
                }
            }
        } else {
            $filename = $pro_uid . PATH_SEP . $dyn_uid . ".xml";
            if (!class_exists('Smarty')) {
                require_once(PATH_THIRDPARTY . 'smarty' . PATH_SEP . 'libs' . PATH_SEP . 'Smarty.class.php');
            }
            $xmlfrm = new XmlForm();
            $xmlfrm->home = PATH_DYNAFORM;
            $xmlfrm->parseFile($filename, SYS_LANG, true);
        }

        $caseColumns = array();
        $caseReaderFields = array();

        $dropList = array();
        $comboBoxYesNoList = array();

        $caseColumns[] = array(
            "header" => "APP_UID",
            "dataIndex" => "APP_UID",
            "width" => 100,
            "hidden" => true,
            "hideable" => false
        );
        $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 40, "sortable" => true);
        $caseColumns[] = array(
            "header" => G::LoadTranslation("ID_TITLE"),
            "dataIndex" => "APP_TITLE",
            "width" => 180,
            "renderer" => "renderTitle",
            "sortable" => true
        );
        $caseColumns[] = array(
            "header" => G::LoadTranslation("ID_SUMMARY"),
            "width" => 60,
            "renderer" => "renderSummary",
            "align" => "center"
        );
        $caseColumns[] = array(
            "header" => "DEL_INDEX",
            "dataIndex" => "DEL_INDEX",
            "width" => 100,
            "hidden" => true,
            "hideable" => false
        );

        $caseReaderFields[] = array("name" => "APP_UID");
        $caseReaderFields[] = array("name" => "APP_NUMBER");
        $caseReaderFields[] = array("name" => "APP_TITLE");
        $caseReaderFields[] = array("name" => "DEL_INDEX");

        foreach ($xmlfrm->fields as $index => $value) {
            $field = $value;

            $editor = null;
            $renderer = null;

            $readOnly = (isset($field->readOnly)) ? $field->readOnly : null;
            $required = (isset($field->required)) ? $field->required : null;
            $validate = (isset($field->validate)) ? strtolower($field->validate) : null;

            if (isset($field->options) && !isset($field->storeData)) {
                $options = [];
                foreach ($field->options as $keyField => $valueField) {
                    $options[] = [$keyField, $valueField];
                }
                $field->storeData = G::json_encode($options);
            }

            $fieldReadOnly = ($readOnly . "" == "1" || $readOnly == 'view') ? "readOnly: true," : null;
            $fieldRequired = ($required . "" == "1") ? "allowBlank: false," : null;
            $fieldValidate = ($validate == "alpha" || $validate == "alphanum" || $validate == "email" || $validate == "int" || $validate == "real") ? "vtype: \"$validate\"," : null;

            $fieldLabel = (($fieldRequired != null) ? "<span style='color: red;'>&#42;</span> " : null) . $field->label;
            $fieldDisabled = ($field->mode == "view") ? "true" : "false";

            switch ($field->type) {
                case "dropdown":
                    $dropList[] = $field->name;
                    $align = "left";

                    if (empty($field->storeData)) {
                        $editor = "* new Ext.form.ComboBox({
                               id: \"cbo" . $field->name . "_" . $pro_uid . "\",

                               valueField:   'value',
                               displayField: 'text',

                               /*store: comboStore,*/
                               store: new Ext.data.JsonStore({
                                 storeId: \"store" . $field->name . "_" . $pro_uid . "\",
                                 proxy: new Ext.data.HttpProxy({
                                   url: 'proxyDataCombobox'
                                 }),
                                 root: 'records',
                                 fields: [{name: 'value'},
                                          {name: 'text'}
                                         ]
                               }),

                               triggerAction: 'all',
                               mode:     'local',
                               editable: false,
                               disabled: $fieldDisabled,
                               lazyRender: false,

                               $fieldReadOnly
                               $fieldRequired
                               $fieldValidate
                               cls: \"\"
                             }) *";
                    } else {
                        $editor = "* new Ext.form.ComboBox({
                                   id: \"cbo" . $field->name . "_" . $pro_uid . "\",

                                   typeAhead: true,
                                   autocomplete:true,
                                   editable:false,
                                   lazyRender:true,
                                   mode:'local',
                                   triggerAction:'all',
                                   forceSelection:true,

                                   valueField:   'value',
                                   displayField: 'text',
                                   store:new Ext.data.SimpleStore({
                                        fields: [{name: 'value'},
                                              {name: 'text'}],
                                        data: " . htmlspecialchars_decode($field->storeData) . ",
                                        sortInfo:{field:'text',direction:'ASC'}
                                    }),

                                   $fieldReadOnly
                                   $fieldRequired
                                   $fieldValidate
                                   cls: \"\"
                                 }) *";
                    }

                    $editor = $this->removeLineBreaks($editor);
                    $width = $field->colWidth;

                    $caseColumns[] = array(
                        "xtype" => "combocolumn",
                        "gridId" => "gridId",
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => "true",
                        "clicksToEdit" => "1"
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "date":
                    $align = "center";
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.DateField({
                                     format: \"$dateFormat\",

                                     $fieldReadOnly
                                     $fieldRequired
                                     $fieldValidate
                                     cls: \"\"
                                 }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $renderer = "* function (value){
                                     return Ext.isDate(value)? value.dateFormat('{$dateFormat}') : value;
                                   } *";
                    $renderer = $this->removeLineBreaks($renderer);
                    if ($field->mode == "view") {
                        $editor = null;
                    }

                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "editor" => $editor,
                        "renderer" => $renderer,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name, "type" => "date");
                    break;
                case "currency":
                    $align = 'right';
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.NumberField({
                                   maxValue: 1000000,
                                   allowDecimals: true,
                                   allowNegative: true,

                                   $fieldReadOnly
                                   $fieldRequired
                                   $fieldValidate
                                   cls: \"\"
                                 }) *";
                    $editor = $this->removeLineBreaks($editor);
                    if ($field->mode != "edit") {
                        $editor = null;
                    }

                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "percentage":
                    $align = 'right';
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.NumberField({
                                   maxValue: 100,
                                   allowDecimals: true,

                                   $fieldReadOnly
                                   $fieldRequired
                                   $fieldValidate
                                   cls: \"\"
                                 }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $renderer = "* function (value){
                                     return (value + ' %');
                                   } *";
                    $renderer = $this->removeLineBreaks($renderer);
                    if ($field->mode != "edit") {
                        $editor = null;
                    }

                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "renderer" => $renderer,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "textarea":
                    $align = 'left';
                    $size = 200;

                    if (isset($field->size)) {
                        $size = $field->size * 15;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.TextArea({
                                     growMin: 60,
                                     growMax: 1000,
                                     grow: true,
                                     autoHeight: true,
                                     disabled: $fieldDisabled,
                                     enterIsSpecial: false,
                                     preventScrollbars: false,

                                     $fieldReadOnly
                                     $fieldRequired
                                     $fieldValidate
                                     cls: \"\"
                                   }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $renderer = "* function (value) {  return (value);  } *";
                    $renderer = $this->removeLineBreaks($renderer);
                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "renderer" => $renderer,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);

                    $hasTextArea = true;
                    break;
                case "datetime":
                    $align = "center";
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.DateField({
                                     format: \"$dateFormat\",

                                     $fieldReadOnly
                                     $fieldRequired
                                     $fieldValidate
                                     cls: \"\"
                                 }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $renderer = "* function (value){
                                     return Ext.isDate(value)? value.dateFormat('{$dateFormat}') : value;
                                   } *";
                    $renderer = $this->removeLineBreaks($renderer);
                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "editor" => $editor,
                        "renderer" => $renderer,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name, "type" => "date");
                    break;
                case "link":
                    $align = 'center';
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;
                    $editor = null;

                    $renderer = "* function (value)
                                   {
                                       return linkRenderer(value);
                                   } *";
                    $renderer = $this->removeLineBreaks($renderer);
                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "renderer" => $renderer,
                        "frame" => true,
                        "hidden" => false,
                        "hideable" => false,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "hidden":
                    $align = 'left';
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;

                    $editor = "* new Ext.form.TextField({ allowBlank: false }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)$width,
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => "true",
                        "hidden" => "true",
                        "hideable" => false,
                        "clicksToEdit" => "1"
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "yesno":
                    $align = "right";
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;
                    $dropList[] = $field->name;
                    $comboBoxYesNoList[] = $field->name;

                    $editor = "* new Ext.form.ComboBox({
                                 id: \"cbo" . $field->name . "_" . $pro_uid . "\",

                                 valueField:   'value',
                                 displayField: 'text',

                                 store: new Ext.data.ArrayStore({
                                   storeId: \"store" . $field->name . "_" . $pro_uid . "\",
                                   fields: ['value', 'text'],
                                   data: [[1, 'YES'],
                                          [0, 'NO']
                                         ]
                                 }),

                                 typeAhead: true,

                                 triggerAction: 'all',
                                 mode: 'local',
                                 editable: false,
                                 disabled : $fieldDisabled,
                                 lazyRender: true,

                                 $fieldReadOnly
                                 $fieldRequired
                                 $fieldValidate
                                 cls: \"\"
                               }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $caseColumns[] = array(
                        "xtype" => "combocolumn",
                        "gridId" => "gridId",
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => "true",
                        "clicksToEdit" => "1"
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "checkbox":
                    $align = "center";
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;
                    $dropList[] = $field->name;
                    $comboBoxYesNoList[] = $field->name;

                    $inputValue = "";
                    $uncheckedValue = "";
                    if (isset($field->value)) {
                        $inputValue = ",inputValue: '" . $field->value . "'";
                    }
                    if (isset($field->falseValue)) {
                        $uncheckedValue = ",uncheckedValue: '" . $field->falseValue . "'";
                    }

                    $editor = "* new Ext.form.CheckboxCustom({ $fieldReadOnly $fieldRequired $fieldValidate cls: \"\" " . $inputValue . $uncheckedValue . " }) *";
                    $editor = $this->removeLineBreaks($editor);
                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);
                    break;
                case "text":
                default:
                    $align = "left";
                    $size = 100;

                    if (isset($field->size)) {
                        $size = $field->size * 10;
                    }

                    $width = $size;
                    $editor = "* new Ext.form.TextField({ $fieldReadOnly $fieldRequired $fieldValidate cls: \"\"}) *";
                    $editor = $this->removeLineBreaks($editor);

                    if ($field->mode != "edit" && $field->mode != "parent") {
                        $editor = null;
                    }

                    $caseColumns[] = array(
                        "header" => $fieldLabel,
                        "dataIndex" => $field->name,
                        "width" => (int)($width),
                        "align" => $align,
                        "editor" => $editor,
                        "frame" => true,
                        "clicksToEdit" => 1,
                        "sortable" => true
                    );
                    $caseReaderFields[] = array("name" => $field->name);
            }
        }

        @unlink(PATH_C . "ws" . PATH_SEP . SYS_SYS . PATH_SEP . "xmlform" . PATH_SEP . $pro_uid . PATH_SEP . $dyn_uid . "." . SYS_LANG);


        $array ['columnModel'] = $caseColumns;
        $array ['readerFields'] = $caseReaderFields;
        $array ["dropList"] = $dropList;
        $array ["comboBoxYesNoList"] = $comboBoxYesNoList;
        $array ['hasTextArea'] = $hasTextArea;

        $temp = G::json_encode($array);

        $temp = str_replace('"*', '', $temp);
        $temp = str_replace('*"', '', $temp);
        $temp = str_replace('\t', '', $temp);
        $temp = str_replace('\n', '', $temp);
        $temp = str_replace('\/', '/', $temp);
        $temp = str_replace('\"', '"', $temp);
        $temp = str_replace('"checkcolumn"', '\'checkcolumn\'', $temp);

        print $temp;
        die();
    }

    /**
     * Get Dropdown Label
     *
     * @param string $appUid
     * @param string $pro_uid
     * @param string $dyn_uid
     * @param string $fieldName
     * @param string $fieldVal
     *
     * @return string
     */
    public function getDropdownLabel($appUid, $pro_uid, $dyn_uid, $fieldName, $fieldVal)
    {
        $oCase = new Cases();
        $filename = $pro_uid . PATH_SEP . $dyn_uid . ".xml";

        $G_FORM = new xmlform();
        $G_FORM->home = PATH_DYNAFORM;
        $G_FORM->parseFile($filename, SYS_LANG, true);

        $aFields = $oCase->loadCase($appUid);

        $arrayTmp = array();
        $array = array();
        $sqlQuery = null;

        foreach ($G_FORM->fields as $key => $val) {
            if ($fieldName == $val->name) {
                if ($G_FORM->fields[$key]->sql != "") {
                    $sqlQuery = G::replaceDataField($G_FORM->fields[$key]->sql, $aFields ["APP_DATA"]);
                }
                if ((is_array($val->options)) && (!empty($val->options))) {
                    foreach ($val->options as $key1 => $val1) {
                        $array[] = array("id" => $key1, "value" => $val1);
                    }
                }
                if ($val->type == "yesno") {
                    $array[] = array("id" => 1, "value" => strtoupper(G::LoadTranslation("ID_YES")));
                    $array[] = array("id" => 0, "value" => strtoupper(G::LoadTranslation("ID_NO")));
                }
            }
        }

        if ($sqlQuery != null) {
            $aResult = executeQuery($sqlQuery);
            if ($aResult == false) {
                $aResult = array();
            }
        } else {
            $aResult = array();
        }

        foreach ($aResult as $field) {
            $i = 0;
            foreach ($field as $key => $value) {
                if ($i == 0) {
                    $arrayTmp["id"] = $value;
                    if (count($field) == 1) {
                        $arrayTmp["value"] = $value;
                    }
                }

                if ($i == 1) {
                    $arrayTmp["value"] = $value;
                }
                $i++;
            }
            $array[] = $arrayTmp;
        }

        foreach ($array as $newKey => $newValue) {
            if ($newValue["id"] == $fieldVal) {
                return $newValue["value"];
            }
        }

        return null;
    }


    /**
     * Check Valid Date
     *
     * @param string $field
     *
     * @return boolean
     *
     */
    public function checkValidDate($field)
    {
        if (($timestamp = strtotime($field)) === false || is_double($field) || is_float($field) || is_bool($field) || is_int($field)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * This function update the case with the consolidate data
     *
     * @param string $dynaformUid
     * @param array $dataUpdate
     * @param string $usr_uid
     *
     * @return void
     *
     */
    function consolidatedUpdate($dynaformUid, $dataUpdate, $usr_uid)
    {

        $delIndex = 1;
        $oCase = new ClassesCases();

        $array = array();
        $array["form"] = $dataUpdate;
        $appUid = $array["form"]["APP_UID"];

        $fields = $oCase->loadCase($appUid);
        if (!isset($fields["DEL_INIT_DATE"])) {
            $oCase->setDelInitDate($appUid, $delIndex);
            //$aFields = $oCase->loadCase($appUid, $delIndex);
            $fields = $oCase->loadCase($appUid, $delIndex);
        }

        $auxAppDataApplication = $fields["APP_DATA"]["APPLICATION"];
        $auxAppDataProcess = $fields["APP_DATA"]["PROCESS"];
        $auxAppDataTask = $fields["APP_DATA"]["TASK"];
        $auxAppDataIndex = $fields["APP_DATA"]["INDEX"];

        foreach ($array["form"] as $key => $value) {
            $array["form"][$key] = (string)$array["form"][$key];
            if (isset($fields["APP_DATA"][$key . '_label'])) {
                $array["form"][$key . '_label'] = (string)$array["form"][$key];
            }
        }

        $fields["APP_DATA"] = array_merge($fields["APP_DATA"], G::getSystemConstants());
        $fields["APP_DATA"] = array_merge($fields["APP_DATA"], $array["form"]);

        $fields["APP_DATA"]["APPLICATION"] = $auxAppDataApplication;
        $fields["APP_DATA"]["PROCESS"] = $auxAppDataProcess;
        $fields["APP_DATA"]["TASK"] = $auxAppDataTask;
        $fields["APP_DATA"]["INDEX"] = $auxAppDataIndex;

        $aData = array();
        $aData["APP_NUMBER"] = $fields["APP_NUMBER"];
        $aData["APP_PROC_STATUS"] = $fields["APP_PROC_STATUS"];
        $aData["APP_DATA"] = $fields["APP_DATA"];
        $aData["DEL_INDEX"] = $delIndex;
        $aData["TAS_UID"] = $fields["APP_DATA"]["TASK"];
        $aData["CURRENT_DYNAFORM"] = $dynaformUid;
        $aData["USER_UID"] = $usr_uid;
        $aData["APP_STATUS"] = $fields["APP_STATUS"];
        $aData["PRO_UID"] = $fields["APP_DATA"]["PROCESS"];

        $oCase->updateCase($appUid, $aData);
    }

    /**
     * Important for windows servers, because the character '\r' breaks the json
     * definition.
     *
     * @param string $string
     *
     * @return string
     */
    public function removeLineBreaks($string)
    {
        return preg_replace("[\n|\r|\n\r]", ' ', $string);
    }

    /**
     * Get total
     *
     * @param $usrUid
     *
     * @return int
     */
    public function getCountList($usrUid)
    {
        $criteria = new Criteria();
        $criteria->add(CaseConsolidatedCorePeer::CON_STATUS, 'ACTIVE');
        $criteria->addJoin(CaseConsolidatedCorePeer::TAS_UID, ListInboxPeer::TAS_UID, Criteria::LEFT_JOIN);
        $criteria->add(ListInboxPeer::USR_UID, $usrUid, Criteria::EQUAL);
        $criteria->add(ListInboxPeer::APP_STATUS, 'TO_DO', Criteria::EQUAL);
        $total = CaseConsolidatedCorePeer::doCount($criteria);

        return (int)$total;
    }
}
