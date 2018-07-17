<?php
/**
 * cases_DynaformHistory.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

$tpl = new TemplatePower(PATH_TPL . "cases" . PATH_SEP . "cases_DynaformHistory.html");

$tpl->prepare();

$oCase = new Cases();
$Fields = $oCase->loadCase($_SESSION['APPLICATION']);

// Load form info
if (isset($_REQUEST['DYN_UID']) && $_REQUEST['DYN_UID'] != '') {
    $form = new Form($_REQUEST['PRO_UID'] . PATH_SEP . $_REQUEST['DYN_UID'], PATH_DYNAFORM, SYS_LANG, false);
}

$historyData = array();
$historyDataAux = array();

$appHistory = new AppHistory();
$c = $appHistory->getDynaformHistory($_REQUEST['PRO_UID'], $_REQUEST['TAS_UID'], $_REQUEST['APP_UID'], $_REQUEST['DYN_UID']);

$oDataset = ArrayBasePeer::doSelectRs($c);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

$changeCount = 0;

while ($oDataset->next()) {
    $aRow = $oDataset->getRow();

    $changeCount++;

    $changedValues = unserialize($aRow['HISTORY_DATA']);
    $tableName = "_TCHANGE_" . $changeCount;
    $historyDataAux[$tableName] = $changedValues;
}

$historyData = array_reverse($historyDataAux);
$changeCount = count($historyData);

foreach ($historyData as $key => $value) {
    $tableName = "_TCHANGE_" . $changeCount;
    $changeCountA = $changeCount + 1;
    $tableNameA = "_TCHANGE_" . $changeCountA;

    if (isset($historyData[$tableNameA])) {
        //$historyData[$key]=array_merge($historyData[$tableNameA],$value);
        //Array merge recursive doesn't work. So here is an own procedure
        $historyData[$key] = $historyData[$tableNameA];

        foreach ($value as $key1 => $value2) {
            if (!is_array($value2)) {
                $historyData[$key][$key1] = $value2;
            } else {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $key4 => $value4) {
                            $historyData[$key][$key1][$key3][$key4] = $value4;
                        }
                    }
                }
            }
        }
    }

    $changeCount--;
}

$oDataset = ArrayBasePeer::doSelectRs($c);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

$changeCount = 0;

while ($oDataset->next()) {
    $aRow = $oDataset->getRow();

    $changeCount++;

    $changedValues = unserialize($aRow['HISTORY_DATA']);

    $tpl->newBlock("DYNLOG");
    $tableName = "_TCHANGE_".$changeCount;
    $changeCountA = $changeCount + 1;
    $tableNameA = "_TCHANGE_" . $changeCountA;

    $tpl->assign("dynTitle", addslashes($aRow["DYN_TITLE"]));
    $tpl->assign("dynDate", $aRow["HISTORY_DATE"]);
    $tpl->assign("dynUser", addslashes($aRow["USR_NAME"]));
    $tpl->assign("changes", G::LoadTranslation("ID_CHANGES"));
    $tpl->assign("dynUID", $aRow["DYN_UID"]);
    $tpl->assign("tablename", $tableName);

    $tpl->assign("viewForm", ($aRow["OBJ_TYPE"] == "DYNAFORM")? "<a href=\"javascript:;\" onclick=\"showDynaformHistory('" . $aRow["DYN_UID"] . "', '$tableName', '" . $aRow["HISTORY_DATE"] . "', '" . addslashes($aRow["DYN_TITLE"]) . "'); return false;\">" . G::LoadTranslation("ID_VIEW") . "</a>" : "");
    $tpl->assign("dynaform", G::LoadTranslation("ID_DYNAFORM"));
    $tpl->assign("date", G::LoadTranslation("ID_DATE"));
    $tpl->assign("user", G::LoadTranslation("ID_USER"));

    $tpl->assign("fieldNameLabel", G::LoadTranslation("ID_FIELDS"));
    $tpl->assign("previousValuesLabel", G::LoadTranslation("ID_PREV_VALUES"));
    $tpl->assign("currentValuesLabel", G::LoadTranslation("ID_CURRENT_VALUES"));

    $count = 0;

    foreach ($changedValues as $key => $value) {
        if ($value != null && !is_array($value)) {
            if (isset($form) && isset($form->fields[$key])) {
                $label = $form->fields[$key]->label . ' (' . $key . ')';
            } else {
                $label = $key;
            }
            if (strpos($label, "DYN_CONTENT_HISTORY") === false) {
                $tpl->newBlock("FIELDLOG");
                $tpl->assign("fieldName", $label);
                $tpl->assign("previous", (isset($historyData[$tableNameA][$key]))? $historyData[$tableNameA][$key] : "");
                $tpl->assign("actual", $value);
                $count++;
            }
        }

        if (is_array($value)) {
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) {
                        if (isset($form) && isset($form->fields[$key]->fields[$key2])) {
                            $label = $form->fields[$key]->fields[$key2]->label . ' (' . $key . '[' . $key1 . '][' . $key2 . '])';
                        } else {
                            $label = $key . '[' . $key1 . ']' . '[' . $key2 . ']';
                        }
                        $tpl->newBlock("FIELDLOG");
                        $tpl->assign("fieldName", $label);
                        $tpl->assign("previous", (isset($historyData[$tableNameA][$key][$key1][$key2]))? $historyData[$tableNameA][$key][$key1][$key2] : "");
                        $tpl->assign("actual", $value2);
                        $count++;
                    }
                }
            }
        }
    }

    $tpl->gotoBlock("DYNLOG");

    $tpl->assign("dynChanges", G::LoadTranslation("ID_FIELDS_CHANGED_NUMBER") . " (" . $count . ")");
    $tpl->assign("count", $count + 1);
}

if (!isset($changedValues)) {
    $tpl->newBlock("NORESULTS");
    $tpl->assign("noResults", G::LoadTranslation("ID_NO_RECORDS_FOUND"));
}

$_SESSION['HISTORY_DATA'] = $historyData;
$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();

