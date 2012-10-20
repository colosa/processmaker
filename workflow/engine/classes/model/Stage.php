<?php
/**
 * Stage.php
 *
 * @package workflow.engine.classes.model
 */

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseStage.php';

/**
 * Skeleton subclass for representing a row from the 'STAGE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Stage extends BaseStage
{
    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $stg_title = '';

    /**
     * Get the stg_title column value.
     *
     * @return string
     */
    public function getStgTitle ()
    {
        if ($this->getStgUid() == "") {
            throw (new Exception( "Error in getStgTitle, the getStgUid() can't be blank" ));
        }
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        $this->stg_title = Content::load( 'STG_TITLE', '', $this->getStgUid(), $lang );
        return $this->stg_title;
    }

    /**
     * Set the stg_title column value.
     *
     * @param string $v new value
     * @return void
     */
    public function setStgTitle ($v)
    {
        if ($this->getStgUid() == "") {
            throw (new Exception( "Error in setStgTitle, the setStgUid() can't be blank" ));
        }
        $v = isset( $v ) ? ((string) $v) : '';
        $lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
        if ($this->stg_title !== $v || $v === "") {
            $this->stg_title = $v;
            $res = Content::addContent( 'STG_TITLE', '', $this->getStgUid(), $lang, $this->stg_title );
            return $res;
        }
        return 0;
    }

    public function load ($StgUid)
    {
        try {
            $oRow = StagePeer::retrieveByPK( $StgUid );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                $this->setStgTitle( $aFields['STG_TITLE'] = $this->getStgTitle() );
                return $aFields;
            } else {
                throw (new Exception( "The row '$StgUid' in table Stage doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function create ($aData)
    {
        $oConnection = Propel::getConnection( StagePeer::DATABASE_NAME );
        try {
            if (isset( $aData['STG_UID'] ) && $aData['STG_UID'] == '') {
                unset( $aData['STG_UID'] );
            }
            if (! isset( $aData['STG_UID'] )) {
                $aData['STG_UID'] = G::generateUniqueID();
            }
            $oStage = new Stage();
            $oStage->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            $oStage->setStgTitle( $aData['STG_TITLE'] );
            if ($oStage->validate()) {
                $oConnection->begin();
                $iResult = $oStage->save();
                $oConnection->commit();
                return $aData['STG_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oStage->getValidationFailures();
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

    public function update ($fields)
    {
        $con = Propel::getConnection( StagePeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['STG_UID'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $contentResult = 0;
                if (array_key_exists( "STG_TITLE", $fields )) {
                    $contentResult += $this->setStgTitle( $fields["STG_TITLE"] );
                }
                $result = $this->save();
                $result = ($result == 0) ? ($contentResult > 0 ? 1 : 0) : $result;
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                $validationE = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $validationE->aValidationFailures = $this->getValidationFailures();
                throw ($validationE);
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function remove ($StgUid)
    {
        $con = Propel::getConnection( StagePeer::DATABASE_NAME );
        try {
            $con->begin();
            $oStage = StagePeer::retrieveByPK( $StgUid );
            if (! is_null( $oStage )) {
                Content::removeContent( 'STG_TITLE', '', $this->getStgUid() );
                $result = $oStage->delete();
                $con->commit();
            }
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    public function reorderPositions ($sProcessUID, $iIndex)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( StagePeer::PRO_UID, $sProcessUID );
            $oCriteria->add( StagePeer::STG_INDEX, $iIndex, '>' );
            $oDataset = StagePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $this->update( array ('STG_UID' => $aRow['STG_UID'],'PRO_UID' => $aRow['PRO_UID'],'STG_POSX' => $aRow['STG_POSX'],'STG_POSY' => $aRow['STG_POSY'],'STG_INDEX' => $aRow['STG_INDEX'] - 1
                ) );
                $oDataset->next();
            }
        } catch (Exception $oException) {
            throw $Exception;
        }
    }

    public function Exists ($sUid)
    {
        try {
            $oObj = StagePeer::retrieveByPk( $sUid );
            return (is_object( $oObj ) && get_class( $oObj ) == 'Stage');
        } catch (Exception $oError) {
            throw ($oError);
        }
    }
}

