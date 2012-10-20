<?php

require_once 'classes/model/om/BaseUsersProperties.php';

/**
 * Skeleton subclass for representing a row from the 'USERS_PROPERTIES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */

/**
 *
 * @package workflow.engine.classes.model
 */
class UsersProperties extends BaseUsersProperties
{
    public $fields = null;
    public $usrID = '';
    public $lang = 'en';

    public function __construct ()
    {
        $this->lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';
    }

    public function UserPropertyExists ($sUserUID)
    {
        $oUserProperty = UsersPropertiesPeer::retrieveByPk( $sUserUID );
        if (! is_null( $oUserProperty ) && is_object( $oUserProperty ) && get_class( $oUserProperty ) == 'UsersProperties') {
            $this->fields = $oUserProperty->toArray( BasePeer::TYPE_FIELDNAME );
            $this->fromArray( $this->fields, BasePeer::TYPE_FIELDNAME );
            return true;
        } else {
            return false;
        }
    }

    public function load ($sUserUID)
    {
        $oUserProperty = UsersPropertiesPeer::retrieveByPK( $sUserUID );
        if (! is_null( $oUserProperty )) {
            $aFields = $oUserProperty->toArray( BasePeer::TYPE_FIELDNAME );
            $this->fromArray( $aFields, BasePeer::TYPE_FIELDNAME );
            return $aFields;
        } else {
            throw new Exception( "User with $sUserUID does not exist!" );
        }
    }

    public function create ($aData)
    {
        $oConnection = Propel::getConnection( UsersPropertiesPeer::DATABASE_NAME );
        try {
            $oUserProperty = new UsersProperties();
            $oUserProperty->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
            if ($oUserProperty->validate()) {
                $oConnection->begin();
                $iResult = $oUserProperty->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oUserProperty->getValidationFailures();
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

    public function update ($aData)
    {
        $oConnection = Propel::getConnection( UsersPropertiesPeer::DATABASE_NAME );
        try {
            $oUserProperty = UsersPropertiesPeer::retrieveByPK( $aData['USR_UID'] );
            if (! is_null( $oUserProperty )) {
                $oUserProperty->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
                if ($oUserProperty->validate()) {
                    $oConnection->begin();
                    $iResult = $oUserProperty->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oUserProperty->getValidationFailures();
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

    public function loadOrCreateIfNotExists ($sUserUID, $aUserProperty = array())
    {
        if (! $this->UserPropertyExists( $sUserUID )) {
            $aUserProperty['USR_UID'] = $sUserUID;
            if (! isset( $aUserProperty['USR_LAST_UPDATE_DATE'] )) {
                $aUserProperty['USR_LAST_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
            }
            if (! isset( $aUserProperty['USR_LOGGED_NEXT_TIME'] )) {
                $aUserProperty['USR_LOGGED_NEXT_TIME'] = 0;
            }
            $this->create( $aUserProperty );
        } else {
            $aUserProperty = $this->fields;
        }

        return $aUserProperty;
    }

    public function validatePassword ($sPassword, $sLastUpdate, $iChangePasswordNextTime)
    {
        if (! defined( 'PPP_MINIMUM_LENGTH' )) {
            define( 'PPP_MINIMUM_LENGTH', 5 );
        }
        if (! defined( 'PPP_MAXIMUM_LENGTH' )) {
            define( 'PPP_MAXIMUM_LENGTH', 20 );
        }
        if (! defined( 'PPP_NUMERICAL_CHARACTER_REQUIRED' )) {
            define( 'PPP_NUMERICAL_CHARACTER_REQUIRED', 0 );
        }
        if (! defined( 'PPP_UPPERCASE_CHARACTER_REQUIRED' )) {
            define( 'PPP_UPPERCASE_CHARACTER_REQUIRED', 0 );
        }
        if (! defined( 'PPP_SPECIAL_CHARACTER_REQUIRED' )) {
            define( 'PPP_SPECIAL_CHARACTER_REQUIRED', 0 );
        }
        if (! defined( 'PPP_EXPIRATION_IN' )) {
            define( 'PPP_EXPIRATION_IN', 0 );
        }
        if (! defined( 'PPP_CHANGE_PASSWORD_AFTER_NEXT_LOGIN' )) {
            define( 'PPP_CHANGE_PASSWORD_AFTER_NEXT_LOGIN', 0 );
        }
        if (function_exists( 'mb_strlen' )) {
            $iLength = mb_strlen( $sPassword );
        } else {
            $iLength = strlen( $sPassword );
        }
        $aErrors = array ();
        if ($iLength < PPP_MINIMUM_LENGTH) {
            $aErrors[] = 'ID_PPP_MINIMUM_LENGTH';
        }
        if ($iLength > PPP_MAXIMUM_LENGTH) {
            $aErrors[] = 'ID_PPP_MAXIMUM_LENGTH';
        }
        if (PPP_NUMERICAL_CHARACTER_REQUIRED == 1) {
            if (preg_match_all( '/[0-9]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE ) == 0) {
                $aErrors[] = 'ID_PPP_NUMERICAL_CHARACTER_REQUIRED';
            }
        }
        if (PPP_UPPERCASE_CHARACTER_REQUIRED == 1) {
            if (preg_match_all( '/[A-Z]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE ) == 0) {
                $aErrors[] = 'ID_PPP_UPPERCASE_CHARACTER_REQUIRED';
            }
        }
        if (PPP_SPECIAL_CHARACTER_REQUIRED == 1) {
            if (preg_match_all( '/[��\\!|"@�#$~%�&�\/()=\'?��*+\-_.:,;]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE ) == 0) {
                $aErrors[] = 'ID_PPP_SPECIAL_CHARACTER_REQUIRED';
            }
        }
        if (PPP_EXPIRATION_IN > 0) {
            G::LoadClass( 'dates' );
            $oDates = new dates();
            $fDays = $oDates->calculateDuration( date( 'Y-m-d H:i:s' ), $sLastUpdate );
            if ($fDays > (PPP_EXPIRATION_IN * 24)) {
                $aErrors[] = 'ID_PPP_EXPIRATION_IN';
            }
        }
        if (PPP_CHANGE_PASSWORD_AFTER_NEXT_LOGIN == 1) {
            if ($iChangePasswordNextTime == 1) {
                $aErrors[] = 'ID_PPP_CHANGE_PASSWORD_AFTER_NEXT_LOGIN';
            }
        }
        return $aErrors;
    }

    /**
     * get user location
     * defined by precedence plugin->ux->default
     */
    public function redirectTo ($usrID, $lang = '')
    {
        $this->usrID = $usrID;
        $this->lang = empty( $lang ) ? $this->lang : $lang;

        $url = $this->_getPluginLocation();

        if (empty( $url )) {
            $url = $this->_getUXLocation();
        }

        $urlUx = $this->_getUXSkinVariant();
        if (empty( $url ) && ! empty( $urlUx )) {
            $_SESSION['_defaultUserLocation'] = $url;
            $url = $urlUx;
        }

        if (empty( $url )) {
            $url = $this->_getDefaultLocation();
        }

        return $url;
    }

    /**
     * get user location
     * defined by precedence plugin->default
     * note that is getting location without User Inbox Simplified varification
     */
    public function getUserLocation ($usrID, $lang = 'en')
    {
        $this->usrID = $usrID;
        $this->lang = empty( $lang ) ? $this->lang : $lang;

        $url = $this->_getPluginLocation();

        if (empty( $url )) {
            $url = $this->_getDefaultLocation();
        }

        $urlUx = $this->_getUXSkinVariant();
        if (! empty( $urlUx )) {
            $_SESSION['_defaultUserLocation'] = $url;
            $url = $urlUx;
        }

        return $url;
    }

    /**
     * to verify if the user is using some "ux..." skin variant
     * if that is the case, the redirection will change to 'main' controller
     */
    public function _getUXSkinVariant ()
    {
        $url = '';

        if (substr( SYS_SKIN, 0, 2 ) == 'ux' && SYS_SKIN != 'uxs') {
            $url = '/sys' . SYS_SYS . '/' . $this->lang . '/' . SYS_SKIN . '/main';
            global $RBAC;
            G::loadClass( 'configuration' );
            $oConf = new Configurations();
            $oConf->loadConfig( $x, 'USER_PREFERENCES', '', '', $_SESSION['USER_LOGGED'], '' );
            if (sizeof( $oConf->aConfig ) > 0) {
                if ($oConf->aConfig['DEFAULT_MENU'] == 'PM_USERS') {
                    $oConf->aConfig['DEFAULT_MENU'] = 'PM_SETUP';
                }

                $getUrl = null;

                switch ($oConf->aConfig['DEFAULT_MENU']) {
                    case 'PM_SETUP':
                        if ($RBAC->userCanAccess( 'PM_SETUP' ) == 1) {
                            $getUrl = 'admin';
                        }
                        break;
                    case 'PM_FACTORY':
                        if ($RBAC->userCanAccess( 'PM_FACTORY' ) == 1) {
                            $getUrl = 'designer';
                        }
                        break;
                    case 'PM_CASES':
                        if ($RBAC->userCanAccess( 'PM_CASES' ) == 1) {
                            $getUrl = 'home';
                        }
                        break;
                    case 'PM_USERS':
                        if ($RBAC->userCanAccess( 'PM_USERS' ) == 1) {
                            $getUrl = 'admin';
                        }
                        break;
                    case 'PM_DASHBOARD':
                        if ($RBAC->userCanAccess( 'PM_DASHBOARD' ) == 1) {
                            $getUrl = 'dashboard';
                        }
                        break;
                }

                $url = $url . (($getUrl != null) ? "?st=" . $getUrl : null);
            }
        }
        return $url;
    }

    /**
     * get the plugins, and check if there is redirectLogins
     * if yes, then redirect goes according his Role
     */
    public function _getPluginLocation ()
    {
        global $RBAC;
        $url = '';

        if (class_exists( 'redirectDetail' )) {
            //to do: complete the validation
            if (isset( $RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'] )) {
                $userRole = $RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'];
            }

            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $aRedirectLogin = $oPluginRegistry->getRedirectLogins();
            if (isset( $aRedirectLogin ) && is_array( $aRedirectLogin )) {
                foreach ($aRedirectLogin as $key => $detail) {
                    if (isset( $detail->sPathMethod ) && $detail->sRoleCode == $userRole) {
                        $url = '/sys' . SYS_SYS . '/' . $this->lang . '/' . SYS_SKIN . '/' . $detail->sPathMethod;
                    }
                }
            }
        }

        return $url;
    }

    /**
     * New feature - User Experience Redirector
     *
     * @author Erik Amaru Ortiz <erik@colosa.com>
     */
    public function _getUXLocation ()
    {
        require_once 'classes/model/Users.php';
        $u = UsersPeer::retrieveByPK( $this->usrID );
        $url = '';

        $uxType = $u->getUsrUx();
        $_SESSION['user_experience'] = 'NORMAL';

        // find a group setting
        if ($uxType == '' || $uxType == 'NORMAL') {
            require_once 'classes/model/GroupUser.php';
            $gu = new GroupUser();
            $ugList = $gu->getAllUserGroups( $this->usrID );

            foreach ($ugList as $row) {
                if ($row['GRP_UX'] != 'NORMAL' && $row['GRP_UX'] != '') {
                    $uxType = $row['GRP_UX'];
                    break;
                }
            }
        }

        switch ($uxType) {
            case 'SIMPLIFIED':
            case 'SWITCHABLE':
            case 'SINGLE':
                $_SESSION['user_experience'] = $uxType;
                $_SESSION['user_last_skin'] = SYS_SKIN;
                $url = '/sys' . SYS_SYS . '/' . $this->lang . '/uxs/' . 'home';
                break;
        }

        return $url;
    }

    /**
     * get user preferences for default redirect
     * verifying if it has any preferences on configurations table
     */
    public function _getDefaultLocation ()
    {
        global $RBAC;
        G::loadClass( 'configuration' );
        $oConf = new Configurations();
        $oConf->loadConfig( $x, 'USER_PREFERENCES', '', '', $_SESSION['USER_LOGGED'], '' );

        $baseUrl = '/sys' . SYS_SYS . '/' . $this->lang . '/' . SYS_SKIN . '/';
        $url = '';

        if (sizeof( $oConf->aConfig ) > 0) {
            // this user has a configuration record
            // backward compatibility, because now, we don't have user and dashboard menu.
            if ($oConf->aConfig['DEFAULT_MENU'] == 'PM_USERS') {
                $oConf->aConfig['DEFAULT_MENU'] = 'PM_SETUP';
            }

            switch ($oConf->aConfig['DEFAULT_MENU']) {
                case 'PM_SETUP':
                    if ($RBAC->userCanAccess( 'PM_SETUP' ) == 1) {
                        $url = 'setup/main';
                    }
                    break;
                case 'PM_FACTORY':
                    if ($RBAC->userCanAccess( 'PM_FACTORY' ) == 1) {
                        $url = 'processes/main';
                    }
                    break;
                case 'PM_CASES':
                    if ($RBAC->userCanAccess( 'PM_CASES' ) == 1) {
                        $url = 'cases/main';
                    }
                    break;
                case 'PM_USERS':
                    if ($RBAC->userCanAccess( 'PM_USERS' ) == 1) {
                        $url = 'setup/main';
                    }
                    break;
                case 'PM_DASHBOARD':
                    if ($RBAC->userCanAccess( 'PM_DASHBOARD' ) == 1) {
                        $url = 'dashboard/main';
                    }
                    break;
            }
        }

        if (empty( $url )) {
            if ($RBAC->userCanAccess( 'PM_FACTORY' ) == 1) {
                $url = 'processes/main';
            } elseif ($RBAC->userCanAccess( 'PM_SETUP' ) == 1) {
                $url = 'setup/main';
            } elseif ($RBAC->userCanAccess( 'PM_CASES' ) == 1) {
                $url = 'cases/main';
            } elseif ($RBAC->userCanAccess( 'PM_USERS' ) == 1) {
                $url = 'setup/main';
            } elseif ($RBAC->userCanAccess( 'PM_DASHBOARD' ) == 1) {
                $url = 'dashboard/dashboard';
            } else {
                $url = 'users/myInfo';
            }
        }

        return $baseUrl . $url;
    }
}

