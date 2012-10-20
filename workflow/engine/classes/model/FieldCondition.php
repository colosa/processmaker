<?php
/**
 * FieldCondition.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseFieldCondition.php';
require_once 'classes/model/Dynaform.php';

/**
 * Skeleton subclass for representing a row from the 'FIELD_CONDITION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class FieldCondition extends BaseFieldCondition
{

    public $oDynaformHandler;

    /**
     * Quick get all records into a criteria object
     *
     * @author Erik A. Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     */
    public function get ($UID)
    {

        $obj = FieldConditionPeer::retrieveByPk( $UID );
        if (! isset( $obj )) {
            throw new Exception( "the record with UID: $UID doesn't exits!" );
        }
        //TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
        return $obj->toArray( BasePeer::TYPE_FIELDNAME );
    }

    /**
     * Quick get all records into a criteria object
     *
     * @author Erik A. Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     */
    public function getAllCriteriaByDynUid ($DYN_UID, $filter = 'all')
    {
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_UID );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_FUNCTION );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_FIELDS );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_CONDITION );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_EVENTS );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_EVENT_OWNERS );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_STATUS );
        $oCriteria->addSelectColumn( FieldConditionPeer::FCD_DYN_UID );

        $oCriteria->add( FieldConditionPeer::FCD_DYN_UID, $DYN_UID );
        switch ($filter) {
            case 'active':
                $oCriteria->add( FieldConditionPeer::FCD_STATUS, '1', Criteria::EQUAL );
                break;
        }

        return $oCriteria;
    }

    /**
     * Quick get all records into a associative array
     *
     * @author Erik A. Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     */
    public function getAllByDynUid ($DYN_UID, $filter = 'all')
    {
        $aRows = Array ();

        $oCriteria = $this->getAllCriteriaByDynUid( $DYN_UID, $filter );

        $oDataset = FieldConditionPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            $aRows[] = $aRow;
            $oDataset->next();
        }

        return $aRows;
    }

    /**
     * Quick save a record
     *
     * @author Erik A. Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     */
    public function quickSave ($aData)
    {
        $con = Propel::getConnection( FieldConditionPeer::DATABASE_NAME );
        try {
            $obj = null;

            if (isset( $aData['FCD_UID'] ) && trim( $aData['FCD_UID'] ) != '') {
                $obj = FieldConditionPeer::retrieveByPk( $aData['FCD_UID'] );
            } else {
                $aData['FCD_UID'] = G::generateUniqueID();
            }

            if (! is_object( $obj )) {
                $obj = new FieldCondition();
            }

            $obj->fromArray( $aData, BasePeer::TYPE_FIELDNAME );

            if ($obj->validate()) {
                $result = $obj->save();
                $con->commit();
                return $result;
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $obj->getValidationFailures();
                throw ($e);
            }

        } catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function getConditionScript ($DYN_UID)
    {
        require_once 'classes/model/Dynaform.php';
        G::LoadSystem( 'dynaformhandler' );

        $oDynaform = DynaformPeer::retrieveByPk( $DYN_UID );
        $PRO_UID = $oDynaform->getProUid();

        $this->oDynaformHandler = new dynaFormHandler( PATH_DYNAFORM . "$PRO_UID/$DYN_UID" . '.xml' );
        $aDynaformFields = $this->oDynaformHandler->getFieldNames();
        for ($i = 0; $i < count( $aDynaformFields ); $i ++) {
            $aDynaformFields[$i] = "'$aDynaformFields[$i]'";
        }

        $sDynaformFieldsAsStrings = implode( ',', $aDynaformFields );

        $aRows = $this->getAllByDynUid( $DYN_UID, 'active' );
        $sCode = '';

        if (sizeof( $aRows ) != 0) {
            foreach ($aRows as $aRow) {
                $hashCond = md5( $aRow['FCD_UID'] );
                $sCondition = $this->parseCondition( $aRow['FCD_CONDITION'] );
                $sCondition = addslashes( $sCondition );

                $sCode .= "function __condition__$hashCond() { ";
                $sCode .= "if( eval(\"{$sCondition}\") ) { ";

                $aFields = explode( ',', $aRow['FCD_FIELDS'] );

                switch ($aRow['FCD_FUNCTION']) {
                    case 'show':
                        foreach ($aFields as $aField) {
                            $sCode .= "showRowById('$aField');";
                        }
                        break;
                    case 'showOnly':
                        $sCode .= "hideRowsById(Array($sDynaformFieldsAsStrings));";
                        foreach ($aFields as $aField) {
                            $sCode .= "showRowById('$aField');";
                        }
                        break;
                    case 'showAll':
                        $sCode .= "showRowsById(Array($sDynaformFieldsAsStrings));";
                        break;
                    case 'hide':
                        foreach ($aFields as $aField) {
                            $sCode .= "hideRowById('$aField');";
                        }
                        break;
                    case 'hideOnly':
                        $sCode .= "showRowsById(Array($sDynaformFieldsAsStrings));";
                        foreach ($aFields as $aField) {
                            $sCode .= "hideRowById('$aField');";
                        }
                        break;
                    case 'hideAll':
                        $aDynaFields = array ();
                        $aEventOwner = explode( ',', $aRow['FCD_EVENT_OWNERS'] );
                        foreach ($aDynaformFields as $sDynaformFields) {
                            if (! in_array( str_replace( "'", "", $sDynaformFields ), $aEventOwner )) {
                                $aDynaFields[] = $sDynaformFields;
                            }
                        }
                        $sDynaformFieldsAsStrings = implode( ',', $aDynaFields );
                        $sCode .= "hideRowsById(Array($sDynaformFieldsAsStrings));";
                        break;
                }
                $sCode .= "  } ";
                $sCode .= "} ";
                $aFieldOwners = explode( ',', $aRow['FCD_EVENT_OWNERS'] );
                $aEvents = explode( ',', $aRow['FCD_EVENTS'] );
                if (in_array( 'onchange', $aEvents )) {
                    foreach ($aFieldOwners as $aField) {

                        //verify the field type
                        $node = $this->oDynaformHandler->getNode( $aField );
                        $nodeType = $node->getAttribute( 'type' );

                        switch ($nodeType) {
                            case 'checkbox':
                                $sJSEvent = 'click';
                                break;
                            case 'text':
                            case 'textarea':
                            case 'currency':
                            case 'percentage':
                                $sJSEvent = 'blur';
                                break;
                            default:
                                $sJSEvent = 'change';
                                break;
                        }
                        $sCode .= "leimnud.event.add(getField('$aField'), '$sJSEvent', function() {";
                        $sCode .= "  __condition__$hashCond(); ";
                        $sCode .= "}.extend(getField('$aField')));";
                    }

                }
                if (in_array( 'onload', $aEvents )) {
                    foreach ($aFieldOwners as $aField) {
                        $sCode .= "  __condition__$hashCond(); ";
                    }
                }
            }

            return $sCode;
        } else {
            return null;
        }
    }

    public function parseCondition ($sCondition)
    {
        preg_match_all( '/@#[a-zA-Z0-9_.]+/', $sCondition, $result );
        if (sizeof( $result[0] ) > 0) {
            foreach ($result[0] as $fname) {
                preg_match_all( '/(@#[a-zA-Z0-9_]+)\.([@#[a-zA-Z0-9_]+)/', $fname, $result2 );
                if (isset( $result2[2][0] ) && $result2[1][0]) {
                    $sCondition = str_replace( $fname, "getField('" . str_replace( '@#', '', $result2[1][0] ) . "')." . $result2[2][0], $sCondition );
                } else {
                    $field = str_replace( '@#', '', $fname );
                    $node = $this->oDynaformHandler->getNode( $field );
                    if (isset( $node )) {
                        $nodeType = $node->getAttribute( 'type' );
                        switch ($nodeType) {
                            case 'checkbox':
                                $sAtt = 'checked';
                                break;
                            default:
                                $sAtt = 'value';
                        }
                    } else {
                        $sAtt = 'value';
                    }
                    $sCondition = str_replace( $fname, "getField('" . $field . "').$sAtt", $sCondition );
                }
            }
        }
        return $sCondition;
    }
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( FieldConditionPeer::DATABASE_NAME );
        try {
            // $aData['FCD_UID'] = '';
            if (isset( $aData['FCD_UID'] ) && $aData['FCD_UID'] == '') {
                unset( $aData['FCD_UID'] );
            }
            if (! isset( $aData['FCD_UID'] )) {
                $aData['FCD_UID'] = G::generateUniqueID();
            }
            $oFieldCondition = new FieldCondition();
            $oFieldCondition->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oFieldCondition->validate()) {
                $oConnection->begin();
                $iResult = $oFieldCondition->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oFieldCondition->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception( 'The registry cannot be created!<br />' . $sMessage ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function remove ($sUID)
    {
        $oConnection = Propel::getConnection( FieldConditionPeer::DATABASE_NAME );
        try {
            $oConnection->begin();
            $this->setFcdUid( $sUID );
            $iResult = $this->delete();
            $oConnection->commit();
            return $iResult;
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function fieldConditionExists ($sUid, $aDynaform)
    {
        try {
            $found = false;
            $obj = FieldConditionPeer::retrieveByPk( $sUid );
            if (isset( $obj )) {
                $aFields = $obj->toArray( BasePeer::TYPE_FIELDNAME );
                foreach ($aDynaform as $key => $row) {
                    if ($row['DYN_UID'] == $aFields['FCD_DYN_UID']) {
                        $found = true;
                    }
                }
            }
            // return( get_class($obj) == 'FieldCondition') ;
            return ($found);
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function Exists ($sUid)
    {
        try {
            $obj = FieldConditionPeer::retrieveByPk( $sUid );
            return (is_object( $obj ) && get_class( $obj ) == 'FieldCondition');
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}
// FieldCondition

