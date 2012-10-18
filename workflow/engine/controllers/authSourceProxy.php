<?php

class authSourceProxy extends HttpProxyController
{

    function testingOption ($params)
    {
        /*global $RBAC;
        $fields = array('AUTH_SOURCE_PROVIDER' => $params->optionAuthS);
        $G_PUBLISH = new Publisher();
        $data=array();
        $aCommonFields = array('AUTH_SOURCE_UID',
                               'AUTH_SOURCE_NAME',
                               'AUTH_SOURCE_PROVIDER',
                               'AUTH_SOURCE_SERVER_NAME',
                               'AUTH_SOURCE_PORT',
                               'AUTH_SOURCE_ENABLED_TLS',
                               'AUTH_ANONYMOUS',
                               'AUTH_SOURCE_SEARCH_USER',
                               'AUTH_SOURCE_PASSWORD',
                               'AUTH_SOURCE_VERSION',
                               'AUTH_SOURCE_BASE_DN',
                               'AUTH_SOURCE_OBJECT_CLASSES',
                               'AUTH_SOURCE_ATTRIBUTES');
        $aFields = $aData = array();

        unset($params->PHPSESSID);
        foreach ($params as $sField => $sValue) {
          if (in_array($sField, $aCommonFields)) {
            $aFields[$sField] = (($sField=='AUTH_SOURCE_ENABLED_TLS' || $sField=='AUTH_ANONYMOUS'))? ($sValue=='yes')?1:0 :$sValue;
          }
          else {
            $aData[$sField] = $sValue;
          }
        }
        $aFields['AUTH_SOURCE_DATA'] = $aData;
        if (isset($aFields['AUTH_SOURCE_UID']) && $aFields['AUTH_SOURCE_UID'] != '') {
          $RBAC->updateAuthSource($aFields);
        }
        else {
          $aAuth = $RBAC->createAuthSource($aFields);
        }*/
        //G::pr($aAuth);die;
        $data['success'] = true;
        $data['optionAuthS'] = $params->optionAuthS;
        //$data['sUID'] = $aAuth;
        return $data;
    }

    function saveAuthSources ($params)
    {
        global $RBAC;
        if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
        }
        $aCommonFields = array ('AUTH_SOURCE_UID','AUTH_SOURCE_NAME','AUTH_SOURCE_PROVIDER','AUTH_SOURCE_SERVER_NAME','AUTH_SOURCE_PORT','AUTH_SOURCE_ENABLED_TLS','AUTH_ANONYMOUS','AUTH_SOURCE_SEARCH_USER','AUTH_SOURCE_PASSWORD','AUTH_SOURCE_VERSION','AUTH_SOURCE_BASE_DN','AUTH_SOURCE_OBJECT_CLASSES','AUTH_SOURCE_ATTRIBUTES');

        $aFields = $aData = array ();

        unset( $params->PHPSESSID );
        foreach ($params as $sField => $sValue) {
            if (in_array( $sField, $aCommonFields )) {
                $aFields[$sField] = (($sField == 'AUTH_SOURCE_ENABLED_TLS' || $sField == 'AUTH_ANONYMOUS')) ? ($sValue == 'yes') ? 1 : 0 : $sValue;
            } else {
                $aData[$sField] = $sValue;
            }
        }
        $aFields['AUTH_SOURCE_DATA'] = $aData;
        if ($aFields['AUTH_SOURCE_UID'] == '') {
            $RBAC->createAuthSource( $aFields );
        } else {
            $RBAC->updateAuthSource( $aFields );
        }
        $data = array ();
        $data['success'] = true;
        return $data;
    } //end saveAuthSoruces function
} //end authSourceProxy class

