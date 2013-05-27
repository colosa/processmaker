<?php

require_once 'classes/model/om/BaseSequences.php';


/**
 * Skeleton subclass for representing a row from the 'SEQUENCES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Sequences extends BaseSequences {

    /**
     * Create Sequences Table
     *
     * @param type $aData
     * @return type
     * @throws type
     *
     */
    public function create($aData)
    {
        $con = Propel::getConnection( SequencesPeer::DATABASE_NAME );
        try {
            $this->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception( "Failed Validation in class " . get_class( $this ) . "." );
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $result;
        } catch(Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Change Sequence with native query
     *
     * @param type $seqName
     * @param type $seqValue
     * @return type
     *
     */
    public function changeSequence($seqName, $seqValue)
    {
        try {
            $con = Propel::getConnection('workflow');
            if ($this->nameExists($seqName)) {
                $sql = "UPDATE SEQUENCES SET SEQ_VALUE = $seqValue WHERE SEQ_NAME = '$seqName' ";
            } else {
                $sql = "INSERT INTO SEQUENCES (`SEQ_NAME`,`SEQ_VALUE`) VALUES ('$seqName', $seqValue) ";
            }
            $stmt = $con->createStatement();
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            return $rs;
        } catch(Exception $e) {
            // throw ($e);
            throw new Exception( G::LoadTranslation('ID_ERROR_CHANGE_SEQUENCE_NUMBER'));
        }
    }

    /**
     * Lock Sequence Table
     *
     * @return type
     *
     */
    public function lockSequenceTable()
    {
        try {
            $con = Propel::getConnection('workflow');
            $sql = "LOCK TABLES SEQUENCES WRITE, APPLICATION READ ";

            $stmt = $con->createStatement();
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            return $rs;
        } catch(Exception $e) {
            throw ($e);
        }
    }

    /**
     * Unlock Sequence Table
     *
     * @return type
     *
     */
    public function unlockSequenceTable()
    {
        try {
            $con = Propel::getConnection('workflow');
            $sql = "UNLOCK TABLES ";
            $stmt = $con->createStatement();
            $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            return $rs;
        } catch(Exeption $e) {
            throw ($e);
        }
    }

    /**
     * Name Exists
     *
     * @param type $seqName
     * @return boolean
     *
     */
    public function nameExists($seqName)
    {
        try {
            $oRow = SequencesPeer::retrieveByPK( $seqName );
            if (! is_null( $oRow )) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            return false;
        }
    }

    /**
     * Load Sequences
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function load($seqName)
    {
        try {
            $oRow = SequencesPeer::retrieveByPK( $seqName );
            if (! is_null( $oRow )) {
                $aFields = $oRow->toArray( BasePeer::TYPE_FIELDNAME );
                $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
                $this->setNew( false );
                return $aFields;
            } else {
                throw (new Exception( "The row '" . $seqName . "' in table SEQUENCES doesn't exist!" ));
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     *  Update Sequences
     *
     * @param type $fields
     * @return type
     * @throws type
     */
    public function update($fields)
    {
        $con = Propel::getConnection( SequencesPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->load( $fields['SEC_NAME'] );
            $this->fromArray( $fields, BasePeer::TYPE_FIELDNAME );
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception( "Failed Validation in class " . get_class( $this ) . "." ));
            }
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Remove Sequences
     *
     * @param type $seqName
     * @return type
     * @throws type
     *
     */
    public function remove($seqName)
    {
        $con = Propel::getConnection( SequencesPeer::DATABASE_NAME );
        try {
            $con->begin();
            $this->setSecName( $seqName );
            $result = $this->delete();
            $con->commit();
            return $result;
        } catch (Exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    /**
     * Get new sequece number
     *
     * @param type $seqName
     * @return type
     * @throws type
     */
    public function getSequeceNumber($seqName)
    {
        try {
            $aSequence = $this->load($seqName);
            $nSeqValue = ($aSequence['SEQ_VALUE'] + 1);

            return $nSeqValue;
        } catch (Exception $e) {
            throw ($e);
        }

    }
} // Sequences
