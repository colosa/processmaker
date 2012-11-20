<?php
/**
 * AppHistory.php
 * @package    workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseAppHistory.php';


/**
 * Skeleton subclass for representing a row from the 'APP_HISTORY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class AppHistory extends BaseAppHistory
{
    public function insertHistory($aData)
    {
        $this->setAppUid($aData['APP_UID']);
        $this->setDelIndex($aData['DEL_INDEX']);
        $this->setProUid($aData['PRO_UID']);
        $this->setTasUid($aData['TAS_UID']);
        $this->setDynUid($aData['CURRENT_DYNAFORM']);
        $this->setUsrUid($aData['USER_UID']);
        $this->setAppStatus($aData['APP_STATUS']);
        $this->setHistoryDate($aData['APP_UPDATE_DATE']);
        $this->setHistoryData($aData['APP_DATA']);

        if ($this->validate() ) {
            $res = $this->save();
        } else {
            // Something went wrong. We can now get the validationFailures and handle them.
            $msg = '';
            $validationFailuresArray = $this->getValidationFailures();
            foreach ($validationFailuresArray as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "<br/>";
            }
        }
    }

    public function getDynaformHistory($PRO_UID, $TAS_UID, $APP_UID, $DYN_UID = "")
    {
        G::LoadClass('case');
        $oCase = new Cases();

        $oCase->verifyTable();

        $aObjectPermissions = $oCase->getAllObjects($PRO_UID, $APP_UID, $TAS_UID, $_SESSION['USER_LOGGED']);

        if (!is_array($aObjectPermissions)) {
            $aObjectPermissions = array('DYNAFORMS' => array(-1), 'INPUT_DOCUMENTS' => array(-1), 'OUTPUT_DOCUMENTS' => array(-1));
        }
        if (!isset($aObjectPermissions['DYNAFORMS'])) {
            $aObjectPermissions['DYNAFORMS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['DYNAFORMS'])) {
                $aObjectPermissions['DYNAFORMS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
            $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
                $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
            }
        }
        if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
            $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        } else {
            if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
                $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
            }
        }

        $c = new Criteria('workflow');
        $c->addSelectColumn(AppHistoryPeer::APP_UID);
        $c->addSelectColumn(AppHistoryPeer::DEL_INDEX);
        $c->addSelectColumn(AppHistoryPeer::PRO_UID);
        $c->addSelectColumn(AppHistoryPeer::TAS_UID);
        $c->addSelectColumn(AppHistoryPeer::DYN_UID);
        $c->addSelectColumn(AppHistoryPeer::USR_UID);
        $c->addSelectColumn(AppHistoryPeer::APP_STATUS);
        $c->addSelectColumn(AppHistoryPeer::HISTORY_DATE);
        $c->addSelectColumn(AppHistoryPeer::HISTORY_DATA);
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->addAsColumn('USR_NAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME)");
        $c->addJoin(AppHistoryPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        //WHERE
        $c->add(AppHistoryPeer::DYN_UID, $aObjectPermissions['DYNAFORMS'], Criteria::IN);
        $c->add(AppHistoryPeer::PRO_UID, $PRO_UID);
        $c->add(AppHistoryPeer::APP_UID, $APP_UID);
        if ((isset($DYN_UID))&&($DYN_UID!="")) {
            $c->add(AppHistoryPeer::DYN_UID, $DYN_UID);
        }

        //ORDER BY
        $c->clearOrderByColumns();
        $c->addDescendingOrderByColumn(AppHistoryPeer::HISTORY_DATE);

        //Execute
        $oDataset = AppHistoryPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();

        $aDynHistory = array();
        $aDynHistory[] = array(
            'DYN_TITLE' => 'char'
        );

        while ($aRow = $oDataset->getRow()) {
            $o = new Dynaform();
            $o->setDynUid($aRow['DYN_UID']);
            $aRow['DYN_TITLE'] = $o->getDynTitle();
            $changedValues=unserialize($aRow['HISTORY_DATA']);
            $html="<table border='0' cellpadding='0' cellspacing='0'>";
            $sw_add=false;
            foreach ($changedValues as $key => $value) {
                if (($value!=null) && (!is_array($value))) {
                    $sw_add=true;
                    $html.="<tr>";
                    $html.="<td><b>$key:</b> </td>";
                    $html.="<td>$value</td>";
                    $html.="</tr>";
                }
                if (is_array($value)) {
                    $html.="<tr>";
                    $html.="<td><b>$key (grid):</b> </td>";
                    $html.="<td>";
                    $html.="<table>";
                    foreach ($value as $key1 => $value1) {
                        $html.="<tr>";
                        $html.="<td><b>$key1</b></td>";
                        $html.="<td>";
                        if (is_array($value1)) {
                            $sw_add=true;
                            $html.="<table>";
                            foreach ($value1 as $key2 => $value2) {
                                $html.="<tr>";
                                $html.="<td><b>$key2</b></td>";
                                $html.="<td>$value2</td>";
                                $html.="</tr>";
                            }
                            $html.="</table>";
                        }
                        $html.="</td>";
                        $html.="</tr>";
                    }
                    $html.="</table>";
                    $html.="</td>";
                    $html.="</tr>";
                    $html.="</td>";
                }
            }
            $html.="</table>";

            $aRow['FIELDS']    = $html;

            if ($sw_add) {
                $aDynHistory[] = $aRow;
            }
            $oDataset->next();
        }

        global $_DBArray;
        $_DBArray['DynaformsHistory'] = $aDynHistory;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('DynaformsHistory');
        $oCriteria->addDescendingOrderByColumn(AppHistoryPeer::HISTORY_DATE);
        return $oCriteria;
    }
}

