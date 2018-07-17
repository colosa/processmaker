<?php
/**
 * myInfo.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
try {
    global $RBAC;
    $RBAC->requirePermissions( 'PM_LOGIN' );

    // deprecated the class XmlFormFieldImage is currently part of the class.xmlform.php package
    // the use of the external xmlfield_Image is highly discouraged

    unset( $_SESSION['CURRENT_USER'] );
    $oUser = new Users();
    $aFields = $oUser->load( $_SESSION['USER_LOGGED'] );
    $aFields['USR_PASSWORD'] = '********';
    $aFields['MESSAGE0'] = G::LoadTranslation( 'ID_USER_REGISTERED' ) . '!';
    $aFields['MESSAGE1'] = G::LoadTranslation( 'ID_MSG_ERROR_USR_USERNAME' );
    $aFields['MESSAGE2'] = G::LoadTranslation( 'ID_MSG_ERROR_DUE_DATE' );
    $aFields['MESSAGE3'] = G::LoadTranslation( 'ID_NEW_PASS_SAME_OLD_PASS' );
    $aFields['MESSAGE4'] = G::LoadTranslation( 'ID_MSG_ERROR_USR_FIRSTNAME' );
    $aFields['MESSAGE5'] = G::LoadTranslation( 'ID_MSG_ERROR_USR_LASTNAME' );
    $aFields['NO_RESUME'] = G::LoadTranslation( 'ID_NO_RESUME' );
    $aFields['START_DATE'] = date( 'Y-m-d' );
    $aFields['END_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 5 ) );
    $aFields['RANDOM'] = rand();

    //getting the user and department
    $oDepInfo = new Department();
    $oUser = UsersPeer::retrieveByPk( $aFields['USR_REPORTS_TO'] );
    if (is_object( $oUser ) && get_class( $oUser ) == 'Users') {
        $userFields = $oUser->toArray( BasePeer::TYPE_FIELDNAME );
        $aFields['USR_REPORTS_TO'] = $userFields['USR_FIRSTNAME'] . ' ' . $userFields['USR_LASTNAME'];
        try {
            $depFields = $oDepInfo->Load( $userFields['DEP_UID'] . 'xy<' );
            $aFields['USR_REPORTS_TO'] .= " (" . $depFields['DEP_TITLE'] . ")";
        } catch (Exception $e) {
        }
    } else {
        $aFields['USR_REPORTS_TO'] = ' ';
    }

    try {
        $depFields = $oDepInfo->Load( $aFields['DEP_UID'] );
        $aFields['USR_DEPARTMENT'] = $depFields['DEP_TITLE'];
    } catch (Exception $e) {
        $oUser = UsersPeer::retrieveByPk( $_SESSION['USER_LOGGED'] );
        $oUser->setDepUid( '' );
        $oUser->save();
        $aFields['USR_DEPARTMENT'] = ' ';
    }

    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'MY_ACCOUNT';
    $G_PUBLISH = new Publisher();

    //$RBAC->systemObj->loadByCode('PROCESSMAKER');//('PROCESSMAKER', $_SESSION['USER_LOGGED']);


    #verifying if it has any preferences on the configurations table

    $oConf = new Configurations();
    $oConf->loadConfig( $x, 'USER_PREFERENCES', '', '', $_SESSION['USER_LOGGED'], '' );

    //echo $RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'];
    //G::pr($RBAC->userObj->load($_SESSION['USER_LOGGED']));
    if (sizeof( $oConf->Fields ) > 0) { #this user has a configuration record
        $aFields['PREF_DEFAULT_LANG'] = $oConf->aConfig['DEFAULT_LANG'];
        $aFields['PREF_DEFAULT_MENUSELECTED'] = isset( $oConf->aConfig['DEFAULT_MENU'] ) ? $oConf->aConfig['DEFAULT_MENU'] : '';
        $aFields['PREF_DEFAULT_CASES_MENUSELECTED'] = isset( $oConf->aConfig['DEFAULT_CASES_MENU'] ) ? $oConf->aConfig['DEFAULT_CASES_MENU'] : '';
    } else {
        switch ($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE']) {
            case 'PROCESSMAKER_ADMIN':
                $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_USERS';
                break;

            case 'PROCESSMAKER_OPERATOR':
                $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_CASES';
                break;

        }
        $aFields['PREF_DEFAULT_LANG'] = SYS_LANG;
    }
    //G::pr($RBAC->aUserInfo);
    $rows[] = Array ('id' => 'char','name' => 'char'
    );

    foreach ($RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission) {

        switch ($permission['PER_CODE']) {
            case 'PM_USERS':
            case 'PM_SETUP':
                $rows[] = Array ('id' => 'PM_SETUP','name' => strtoupper( G::LoadTranslation( 'ID_SETUP' ) )
                );
                break;
            case 'PM_CASES':
                $rows[] = Array ('id' => 'PM_CASES','name' => strtoupper( G::LoadTranslation( 'ID_CASES' ) )
                );
                break;
            case 'PM_FACTORY':
                $rows[] = Array ('id' => 'PM_FACTORY','name' => strtoupper( G::LoadTranslation( 'ID_APPLICATIONS' ) )
                );
                break;
        }
    }

    global $G_TMP_MENU;
    $oMenu = new Menu();
    $oMenu->load( 'cases' );

    $rowsCasesMenu[] = Array ('id' => 'char','name' => 'char'
    );

    foreach ($oMenu->Id as $i => $item) {
        if ($oMenu->Types[$i] != 'blockHeader') {
            $rowsCasesMenu[] = Array ('id' => $item,'name' => $oMenu->Labels[$i]
            );
        }
    }

    //G::pr($rows); die;
    global $_DBArray;
    $_DBArray['menutab'] = $rows;
    $_SESSION['_DBArray'] = $_DBArray;
    $_DBArray['CASES_MENU'] = $rowsCasesMenu;
    $_SESSION['_DBArray'] = $_DBArray;

    $oCriteria = new Criteria( 'dbarray' );
    $oCriteria->setDBArrayTable( 'menutab' );

    $oCriteria2 = new Criteria( 'dbarray' );
    $oCriteria2->setDBArrayTable( 'CASES_MENU' );

    if ($RBAC->userCanAccess( 'PM_EDITPERSONALINFO' ) == 1) { //he has permitions for edit his profile
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/myInfo.xml', '', $aFields, 'myInfo_Save' );
    } else { //he has not permitions for edit his profile, so just view mode will be displayed
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/myInfo2.xml', '', $aFields, '' );
    }

    G::RenderPage( 'publish' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

