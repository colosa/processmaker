<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

/*
  * @Author Erik A. Ortiz <erik@colosa.com>
  * @Date Feb 12th, 2010
  */
try {
    if (! isset( $_POST['request'] )) {
        throw new Exception( 'No request set' );
    }
    $request = $_POST['request'];
    $G_PUBLISH = new Publisher();

    switch ($request) {
        case 'new':
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_ConditionalShowHide', '', '' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'edit':
            require_once 'classes/model/FieldCondition.php';
            $oFieldCondition = new FieldCondition();
            $aRow = $oFieldCondition->get( $_POST['FCD_UID'] );
            $aData = Array ();
            $aData['condition'] = 'neyek';
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_ConditionalShowHide', '', $aRow );
            G::RenderPage( 'publish', 'raw' );
            //echo '<script>+alert(getField("FCD_CONDITION").value)</script>';
            break;
        case 'getDynaFieds':



            $_DYN_FILENAME = $_SESSION['Current_Dynafom']['Parameters']['FILE'];
            $sFilter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

            //$oJSON = new Services_JSON();
            $oDynaformHandler = new DynaformHandler( PATH_DYNAFORM . $_DYN_FILENAME . '.xml' );

            $aFilter = explode( ',', $sFilter );

            $aAvailableFields = $oDynaformHandler->getFieldNames( $aFilter );

            print (Bootstrap::json_encode( $aAvailableFields )) ;
            break;
        case 'showDynavars':


            $_DYN_FILENAME = $_SESSION['Current_Dynafom']['Parameters']['FILE'];
            $sFilter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

            $oDynaformHandler = new DynaformHandler( PATH_DYNAFORM . $_DYN_FILENAME . '.xml' );
            $aFilter = explode( ',', $sFilter );
            $aAvailableFields = $oDynaformHandler->getFieldNames( $aFilter );

            $aFieldNames = Array ('id' => 'char','name' => 'char' );

            $aRows = Array ();
            foreach ($aAvailableFields as $sFieldname) {
                array_push( $aRows, Array ('id' => $sFieldname,'name' => $sFieldname) );
            }

            $rows = array_merge( Array ($aFieldNames), $aRows );

            global $_DBArray;
            $_DBArray['DYNAFIELDS'] = $rows;
            $_SESSION['_DBArray'] = $_DBArray;
            $oCriteria = new Criteria( 'dbarray' );
            $oCriteria->setDBArrayTable( 'DYNAFIELDS' );

            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_vars', '', '' );
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'testSetup':
            $sFields = $_POST['sFields'];
            $aFields = Array ();
            $aFieldsTmp = ($sFields == '') ? Array () : explode( ',', $sFields );

            $i = 1;
            foreach ($aFieldsTmp as $aField) {
                $aFields['gFields'][$i ++] = Array ('dynaid' => $aField,'dynafield' => $aField,'dynavalue' => ''
                );
            }

            if (sizeof( $aFields ) > 0) {
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_ConditionalShowHideTest', '', $aFields );
                G::RenderPage( 'publish', 'raw' );
            } else {
                print ('false') ;
            }
            break;
        case 'save':
            require_once 'classes/model/FieldCondition.php';
            $oFieldCondition = new FieldCondition();
            $aDYN = $_SESSION['Current_Dynafom']['Parameters'];
            $_POST['FCD_UID'] = ($_POST['FCD_UID'] == '0') ? '' : $_POST['FCD_UID'];
            $aData = Array ('FCD_UID' => Isset( $_POST['FCD_UID'] ) ? $_POST['FCD_UID'] : '','FCD_FUNCTION' => $_POST['function'],'FCD_FIELDS' => $_POST['fields_selected'],'FCD_CONDITION' => $_POST['condition'],'FCD_EVENTS' => $_POST['events'],'FCD_EVENT_OWNERS' => $_POST['event_owner_selected'],'FCD_STATUS' => $_POST['enabled'],'FCD_DYN_UID' => $aDYN['DYN_UID']);
            $oFieldCondition->quickSave( $aData );
            //Add Audit Log
            if(isset($_POST['enabled']) && $_POST['enabled'] == 1){
              $enable = 'enable';
            }else{
              $enable = 'disable';
            }
            G::auditLog("ConditionsEditorDynaform", "Dynaform Title: " .$aDYN['DYNAFORM_NAME']. ", Condition Editor: [Function: ".$_POST['function']. ", Fields: ".$_POST['fields_selected']. ", Conditions: ".$_POST['condition']. ", Events: ".$_POST['events']. ", Event Owner: ".$_POST['event_owner_selected']. ", Status: ".$enable."]");

            break;
        case 'delete':
            require_once 'classes/model/FieldCondition.php';
            $oFieldCondition = FieldConditionPeer::retrieveByPk( $_POST['FCD_UID'] );
            if (is_object( $oFieldCondition )) {
                $oFieldCondition->delete();
            }
            break;
    }
} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
}

/*
 * <pre>Array
(
    [request] => save
    [fields_selected] => name
    [event_owner_selected] => name
    [function] => show
    [condition] => (@#aaa @#ccc)/2 >=100
    [load] => 1
    [change] => 1
)
</pre><pre>Array
(
    [SYS_LANG] => en
    [URL] => aZNhn2OsaGClqJLQpZprpJOgZseTpGmjaWilpmSfpWtop2SeaZVmomapaJHTpJagqJZu1ZefZZdgnmGmbWilq2jM6aKpog
    [DYN_UID] => 5316266664ac0e33a5cf224021398577
    [PRO_UID] => 6013394054ac0e22b33dc89058523206
    [DYNAFORM_NAME] => main
    [FILE] => 6013394054ac0e22b33dc89058523206/5316266664ac0e33a5cf224021398577_tmp0
)
</pre>
 */

