<?php
/**
 * SwimlanesElements.php
 *
 * @package workflow.engine.classes.model
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */

require_once 'classes/model/om/BaseSwimlanesElements.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'SWIMLANES_ELEMENTS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the input directory.
 *
 * @package workflow.engine.classes.model
 */
class SwimlanesElements extends BaseSwimlanesElements
{

    /**
     * This value goes in the content table
     *
     * @var string
     */
    protected $swi_text = '';

    /*
  * Load the application document registry
  * @param string $sAppDocUid
  * @return variant
  */
    public function load ($sSwiEleUid)
    {
        try {
            $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK( $sSwiEleUid );
            if (! is_null( $oSwimlanesElements )) {
                $aFields = $oSwimlanesElements->toArray( BasePeer::TYPE_FIELDNAME );
                $aFields['SWI_TEXT'] = $oSwimlanesElements->getSwiEleText();
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
     * Create the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function create ($aData)
    {
        $oConnection = Propel::getConnection( SwimlanesElementsPeer::DATABASE_NAME );
        try {
            $aData['SWI_UID'] = G::generateUniqueID();
            $oSwimlanesElements = new SwimlanesElements();
            $oSwimlanesElements->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oSwimlanesElements->validate()) {
                $oConnection->begin();
                if (isset( $aData['SWI_TEXT'] )) {
                    $oSwimlanesElements->setSwiEleText( $aData['SWI_TEXT'] );
                }
                $iResult = $oSwimlanesElements->save();
                $oConnection->commit();
                return $aData['SWI_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oSwimlanesElements->getValidationFailures();
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
     * Update the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function update ($aData)
    {
        $oConnection = Propel::getConnection( SwimlanesElementsPeer::DATABASE_NAME );
        try {
            $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK( $aData['SWI_UID'] );
            if (! is_null( $oSwimlanesElements )) {
                $oSwimlanesElements->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oSwimlanesElements->validate()) {
                    $oConnection->begin();
                    if (isset( $aData['SWI_TEXT'] )) {
                        $oSwimlanesElements->setSwiEleText( $aData['SWI_TEXT'] );
                    }
                    $iResult = $oSwimlanesElements->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oSwimlanesElements->getValidationFailures();
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
     * Remove the application document registry
     *
     * @param array $aData
     * @return string
     *
     */
    public function remove ($sSwiEleUid)
    {
        $oConnection = Propel::getConnection( SwimlanesElementsPeer::DATABASE_NAME );
        try {
            $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK( $sSwiEleUid );
            if (! is_null( $oSwimlanesElements )) {
                $oConnection->begin();
                Content::removeContent( 'SWI_TEXT', '', $oSwimlanesElements->getSwiUid() );
                $iResult = $oSwimlanesElements->delete();
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

    public function swimlanesElementsExists ($sSwiEleUid)
    {
        $con = Propel::getConnection( SwimlanesElementsPeer::DATABASE_NAME );
        try {
            $oSwiEleUid = SwimlanesElementsPeer::retrieveByPk( $sSwiEleUid );
            if (is_object( $oSwiEleUid ) && get_class( $oSwiEleUid ) == 'SwimlanesElements') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get the [swi_text] column value.
     *
     * @return string
     */
    public function getSwiEleText ()
    {
        if ($this->swi_text == '') {
            try {
                $this->swi_text = Content::load( 'SWI_TEXT', '', $this->getSwiUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en') );
            } catch (Exception $oError) {
                throw ($oError);
            }
        }
        return $this->swi_text;
    }

    /**
     * Set the [swi_text] column value.
     *
     * @param string $sValue new value
     * @return void
     */
    public function setSwiEleText ($sValue)
    {
        if ($sValue !== null && ! is_string( $sValue )) {
            $sValue = (string) $sValue;
        }
        if ($this->swi_text !== $sValue || $sValue === '') {
            try {
                $this->swi_text = $sValue;

                $iResult = Content::addContent( 'SWI_TEXT', '', $this->getSwiUid(), (defined( 'SYS_LANG' ) ? SYS_LANG : 'en'), $this->swi_text );
            } catch (Exception $oError) {
                $this->swi_text = '';
                throw ($oError);
            }
        }
    }
}
// SwimlanesElements

