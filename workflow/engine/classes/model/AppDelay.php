<?php
/**
 * AppDelay.php
 * @package    workflow.engine.classes.model
 */

//require_once 'classes/model/om/BaseAppDelay.php';


/**
 * Skeleton subclass for representing a row from the 'APP_DELAY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 * /**
 * @package    workflow.engine.classes.model
 */
class AppDelay extends BaseAppDelay
{
    /**
     * Create the application delay registry
     * @param array $aData
     * @return string
    **/
    public function create($aData)
    {
        $oConnection = Propel::getConnection(AppDelayPeer::DATABASE_NAME);
        try {
            if ( isset ( $aData['APP_DELAY_UID'] ) && $aData['APP_DELAY_UID']== '' ) {
                unset ( $aData['APP_DELAY_UID'] );
            }
            if ( !isset ( $aData['APP_DELAY_UID'] ) ) {
                $aData['APP_DELAY_UID'] = G::generateUniqueID();
            }
            $oAppDelay = new AppDelay();
            $oAppDelay->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oAppDelay->validate()) {
                $oConnection->begin();
                $iResult = $oAppDelay->save();
                $oConnection->commit();
                return $aData['APP_DELAY_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oAppDelay->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw(new Exception('The registry cannot be created!<br />'.$sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    /**
     * Update the application delay registry
     * @param array $aData
     * @return string
    **/
    public function update($aData)
    {
        $oConnection = Propel::getConnection(AppDelayPeer::DATABASE_NAME);
        try {
            $oAppDelay = AppDelayPeer::retrieveByPK($aData['APP_DELAY_UID']);
            if (!is_null($oAppDelay)) {
                $oAppDelay->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oAppDelay->validate()) {
                    $oConnection->begin();
                    $iResult = $oAppDelay->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oAppDelay->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw(new Exception('The registry cannot be updated!<br />'.$sMessage));
                }
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function isPaused($appUid, $delIndex)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDelayPeer::APP_UID, $appUid);
        $oCriteria->add(AppDelayPeer::APP_DEL_INDEX, $delIndex);
        $oCriteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
        $oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null);
        $oCriteria->add(
            $oCriteria->getNewCriterion(
                AppDelayPeer::APP_DISABLE_ACTION_USER,
                null,
                Criteria::ISNULL
            )->addOr(
                $oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)
            )
        );
        $oDataset = AppDelayPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        if ( $aRow ) {
            return true;
        } else {
            return false;
        }

    }
}

