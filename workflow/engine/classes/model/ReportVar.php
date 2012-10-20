<?php
/**
 * ReportVar.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseReportVar.php';

/**
 * Skeleton subclass for representing a row from the 'REPORT_VAR' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class ReportVar extends BaseReportVar
{
    /*
    * Load the report var registry
    * @param string $sRepVarUid
    * @return variant
    */
    public function load ($sRepVarUid)
    {
        try {
            $oReportVar = ReportVarPeer::retrieveByPK( $sRepVarUid );
            if (! is_null( $oReportVar )) {
                $aFields = $oReportVar->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                return $aFields;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Create the report var registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( ReportVarPeer::DATABASE_NAME );
        try {
            if (isset( $aData['REP_VAR_UID'] ) && $aData['REP_VAR_UID'] == '') {
                unset( $aData['REP_VAR_UID'] );
            }
            if (! isset( $aData['REP_VAR_UID'] )) {
                $aData['REP_VAR_UID'] = G::generateUniqueID();
            }
            $oReportVar = new ReportVar();
            $oReportVar->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oReportVar->validate()) {
                $oConnection->begin();
                $iResult = $oReportVar->save();
                $oConnection->commit();
                return $aData['REP_VAR_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oReportVar->getValidationFailures();
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

    /**
     * Update the report var registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function update ($aData)
    {
        $oConnection = Propel::getConnection( ReportVarPeer::DATABASE_NAME );
        try {
            $oReportVar = ReportVarPeer::retrieveByPK( $aData['REP_VAR_UID'] );
            if (! is_null( $oReportVar )) {
                $oReportVar->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oReportVar->validate()) {
                    $oConnection->begin();
                    $iResult = $oReportVar->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oReportVar->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception( 'The registry cannot be updated!<br />' . $sMessage ));
                }
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Remove the report var registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function remove ($sRepVarUid)
    {
        $oConnection = Propel::getConnection( ReportVarPeer::DATABASE_NAME );
        try {
            $oReportVar = ReportVarPeer::retrieveByPK( $sRepVarUid );
            if (! is_null( $oReportVar )) {
                $oConnection->begin();
                $iResult = $oReportVar->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new Exception( 'This row doesn\'t exist!' ));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    public function reportVarExists ($sRepVarUid)
    {
        $con = Propel::getConnection( ReportVarPeer::DATABASE_NAME );
        try {
            $oRepVarUid = ReportVarPeer::retrieveByPk( $sRepVarUid );
            if (is_object( $oRepVarUid ) && get_class( $oRepVarUid ) == 'ReportVar') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}

