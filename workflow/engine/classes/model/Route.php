<?php
/**
 * Route.php
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

//require_once 'classes/model/om/BaseRoute.php';
//require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'ROUTE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package workflow.engine.classes.model
 */
class Route extends BaseRoute
{

    /*
     * Load the application document registry
     * @param string $sRouUid
     * @return variant
     */
    public function load ($sRouUid)
    {
        try {
            $oRoute = RoutePeer::retrieveByPK( $sRouUid );
            if (! is_null( $oRoute )) {
                $aFields = $oRoute->toArray( BasePeer::TYPE_FIELDNAME );
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
        $oConnection = Propel::getConnection( RoutePeer::DATABASE_NAME );
        try {
            $sRouteUID = G::generateUniqueID();
            $aData['ROU_UID'] = $sRouteUID;
            $oRoute = new Route();

            // validating default values
            $aData['ROU_TO_LAST_USER'] = $this->validateValue( isset( $aData['ROU_TO_LAST_USER'] ) ? $aData['ROU_TO_LAST_USER'] : '', array ('TRUE','FALSE'
            ), 'FALSE' );
            $aData['ROU_OPTIONAL'] = $this->validateValue( isset( $aData['ROU_OPTIONAL'] ) ? $aData['ROU_OPTIONAL'] : '', array ('TRUE','FALSE'
            ), 'FALSE' );
            $aData['ROU_SEND_EMAIL'] = $this->validateValue( isset( $aData['ROU_SEND_EMAIL'] ) ? $aData['ROU_SEND_EMAIL'] : '', array ('TRUE','FALSE'
            ), 'TRUE' );

            $oRoute->fromArray( $aData, BasePeer::TYPE_FIELDNAME );

            if ($oRoute->validate()) {
                $oConnection->begin();
                $iResult = $oRoute->save();
                $oConnection->commit();
                return $sRouteUID;
            } else {
                $sMessage = '';
                $aValidationFailures = $oRoute->getValidationFailures();
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
        $oConnection = Propel::getConnection( RoutePeer::DATABASE_NAME );
        try {
            $oRoute = RoutePeer::retrieveByPK( $aData['ROU_UID'] );
            if (! is_null( $oRoute )) {
                // validating default values
                if (isset( $aData['ROU_TO_LAST_USER'] )) {
                    $aData['ROU_TO_LAST_USER'] = $this->validateValue( $aData['ROU_TO_LAST_USER'], array ('TRUE','FALSE'
                    ), 'FALSE' );
                }
                if (isset( $aData['ROU_OPTIONAL'] )) {
                    $aData['ROU_OPTIONAL'] = $this->validateValue( $aData['ROU_OPTIONAL'], array ('TRUE','FALSE'
                    ), 'FALSE' );
                }
                if (isset( $aData['ROU_SEND_EMAIL'] )) {
                    $aData['ROU_SEND_EMAIL'] = $this->validateValue( $aData['ROU_SEND_EMAIL'], array ('TRUE','FALSE'
                    ), 'TRUE' );
                }

                $oRoute->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oRoute->validate()) {
                    $oConnection->begin();
                    $iResult = $oRoute->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oRoute->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception( 'The ROUTE tables cannot be updated!<br />' . $sMessage ));
                }
            } else {
                throw (new Exception( "The row " . $aData['ROU_UID'] . " doesn't exist!" ));
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
    public function remove ($sRouUid)
    {
        $oConnection = Propel::getConnection( RoutePeer::DATABASE_NAME );
        try {
            $oRoute = RoutePeer::retrieveByPK( $sRouUid );
            if (! is_null( $oRoute )) {
                $oConnection->begin();
                $iResult = $oRoute->delete();
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

    public function routeExists ($sRouUid)
    {
        $con = Propel::getConnection( RoutePeer::DATABASE_NAME );
        try {
            $oRouUid = RoutePeer::retrieveByPk( $sRouUid );
            if (is_object( $oRouUid ) && get_class( $oRouUid ) == 'Route') {
                return true;
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Validate value for a variable that only accepts some determinated values
     *
     * @param $value string - value to test
     * @param $validValues array - list of valid values
     * @param $default string default value, if the tested value is not valid the default value is returned
     * @return the tested and accepted value
     */
    public function validateValue ($value, $validValues, $default)
    {
        if (! in_array( $value, $validValues )) {
            $value = $default;
        }
        return $value;
    }
}

