<?php


class actionsByEmailCoreClass extends PMPlugin
{
    public function __construct()
    {
    }

    public function setup()
    {
        
    }

    public function getFieldsForPageSetup()
    {
        return array();
    }

    public function updateFieldsForPageSetup()
    {
    }

    public function sendActionsByEmail($data)
    {
        try {
            // Validations
            try {
                if (!is_object($data)) {
                    throw new Exception('The parameter $data is null.');
                }
                if (!isset($data->TAS_UID)) {
                    throw new Exception('The parameter $data->TAS_UID is null.');
                }

                if (!isset($data->APP_UID)) {
                    throw new Exception('The parameter $data->APP_UID is null.');
                }

                if (!isset($data->DEL_INDEX)) {
                    throw new Exception('The parameter $data->DEL_INDEX is null.');
                }

                if (!isset($data->USR_UID)) {
                    throw new Exception('The parameter $data->USR_UID is null.');
                }

                if ($data->TAS_UID == '') {
                    throw new Exception('The parameter $data->TAS_UID is empty.');
                }

                if ($data->APP_UID == '') {
                    throw new Exception('The parameter $data->APP_UID is empty.');
                }

                if ($data->DEL_INDEX == '') {
                    throw new Exception('The parameter $data->DEL_INDEX is empty.');
                }

                if ($data->USR_UID == '') {
                    error_log('The parameter $data->USR_UID is empty, the routed task may be a self-service type, actions by email does not work with self-service task types.', 0);
                }
            } catch(Exception $e) {
                echo $e->getMessage().' Please contact to your system administrator.';
                die;
            }
            G::LoadClass('pmFunctions');

            $emailSetup = getEmailConfiguration();

            if (!empty($emailSetup)) {
                require_once 'classes/model/AbeConfiguration.php';
                G::LoadClass('case');

                $cases = new Cases();
                $caseFields = $cases->loadCase($data->APP_UID);
                $criteria = new Criteria();
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_UID);
                $criteria->addSelectColumn(AbeConfigurationPeer::PRO_UID);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_TYPE);
                $criteria->addSelectColumn(AbeConfigurationPeer::TAS_UID);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_TEMPLATE);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_DYN_TYPE);
                $criteria->addSelectColumn(AbeConfigurationPeer::DYN_UID);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_EMAIL_FIELD);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_ACTION_FIELD);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_SUBJECT_FIELD);
                $criteria->addSelectColumn(AbeConfigurationPeer::ABE_MAILSERVER_OR_MAILCURRENT);
                $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
                $criteria->addJoin( AbeConfigurationPeer::DYN_UID, DynaformPeer::DYN_UID, Criteria::LEFT_JOIN );
                $criteria->add(AbeConfigurationPeer::PRO_UID, $caseFields['PRO_UID']);
                $criteria->add(AbeConfigurationPeer::TAS_UID, $data->TAS_UID);
                $result = AbeConfigurationPeer::doSelectRS($criteria);
                $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $result->next();
                if ($configuration = $result->getRow()) {
                    $configuration['ABE_EMAIL_FIELD'] = str_replace('@@', '', $configuration['ABE_EMAIL_FIELD']);
                    if ($configuration['ABE_EMAIL_FIELD'] != '' && isset($caseFields['APP_DATA'][$configuration['ABE_EMAIL_FIELD']])) {
                        $email = trim($caseFields['APP_DATA'][$configuration['ABE_EMAIL_FIELD']]);
                    } else {
                        require_once 'classes/model/Users.php';

                        $userInstance = new Users();
                        $userInfo     = $userInstance->getAllInformation($data->USR_UID);
                        $email        = $userInfo['mail'];
                    }

                    if ($email != '') {
                        $subject = G::replaceDataField( $configuration['ABE_SUBJECT_FIELD'], $caseFields['APP_DATA'] );
                        if($subject == ''){
                            $subject = $caseFields['APP_TITLE'];
                        }

                        // Create
                        require_once 'classes/model/AbeRequests.php';

                        $abeRequest = array();
                        $abeRequest['ABE_REQ_UID']      = '';
                        $abeRequest['ABE_UID']          = $configuration['ABE_UID'];
                        $abeRequest['APP_UID']          = $data->APP_UID;
                        $abeRequest['DEL_INDEX']        = $data->DEL_INDEX;
                        $abeRequest['ABE_REQ_SENT_TO']  = $email;
                        $abeRequest['ABE_REQ_SUBJECT']  = $subject;
                        $abeRequest['ABE_REQ_BODY']     = '';
                        $abeRequest['ABE_REQ_ANSWERED'] = 0;
                        $abeRequest['ABE_REQ_STATUS']   = 'PENDING';

                        try {
                            $abeRequestsInstance = new AbeRequests();
                            $abeRequest['ABE_REQ_UID'] = $abeRequestsInstance->createOrUpdate($abeRequest);
                        } catch (Exception $error) {
                            throw $error;
                        }

                        if ($configuration['ABE_TYPE'] != '') {
                            // Email
                            $_SESSION['CURRENT_DYN_UID'] = $configuration['DYN_UID'];

                            $scriptCode = '';
//                            foreach ($dynaform->fields as $fieldName => $field) {
//                                if ($field->type == 'submit') {
//                                    unset($dynaform->fields[$fieldName]);
//                                }
//                            }

                            $__ABE__ = '';
                            $link = (G::is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/ActionsByEmail';

                            switch ($configuration['ABE_TYPE']) {
                                case 'LINK':
                                    // $__ABE__ .= $dynaform->render(PATH_FEATURES . 'actionsByEmail/xmlform.html', $scriptCode) . '<br />';
                                    $__ABE__ .= '<a href="' . $link . 'DataForm?APP_UID=' . G::encrypt($data->APP_UID, URL_KEY) . '&DEL_INDEX=' . G::encrypt($data->DEL_INDEX, URL_KEY) . '&DYN_UID=' . G::encrypt($configuration['DYN_UID'], URL_KEY) . '&ABER=' . G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY) . '" target="_blank">Please complete this form</a>';
                                    break;
                                // coment
                                case 'FIELD':
                                        $variableService = new \ProcessMaker\Services\Api\Project\Variable();
                                        $variables = $variableService->doGetVariables($caseFields['PRO_UID']);
                                        $field = new stdClass();
                                        $field->label = 'Test';
                                        $field->type = 'dropdown';
                                        $field->options = array();
                                        $actionField = str_replace('@@', '', $configuration['ABE_ACTION_FIELD']);
                                        $dynaform = $configuration['DYN_UID'];
                                        $variables = G::json_decode($configuration['DYN_CONTENT'], true);
                                        if(isset($variables['items'][0]['items'])){
                                            $fields = $variables['items'][0]['items'];
                                            foreach ($fields as $key => $value) {
                                                foreach($value as $var){ G::pr($var);
                                                    if(isset($var['variable'])){
                                                        if ($var['variable'] == $actionField) {
                                                             $field->label = $var['label'];
                                                             $field->type  = $var['type'];
                                                             $values = $var['options'];
                                                             foreach ($values as $val){
                                                               $field->options[$val['value']] = $val['value'];
                                                             }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        G::LoadClass('pmDynaform');
                                        $obj = new pmDynaform($configuration['DYN_UID']);
                                        $configuration['CURRENT_DYNAFORM'] = $configuration['DYN_UID'];
                                        $file = $obj->printPmDynaformAbe($configuration);
                                        $__ABE__ .= $file;
                                        $__ABE__ .= '<strong>' . $field->label . '</strong><br /><table align="left" border="0"><tr>';
                                        switch ($field->type) {
                                            case 'dropdown':
                                            case 'radiogroup':
                                                $index = 1;
                                                $__ABE__.='<br /><td><table align="left" cellpadding="2"><tr>';
                                                foreach ($field->options as $optValue => $optName) {
                                                    $__ABE__ .= '<td align="center"><a style="text-decoration: none; color: #000; background-color: #E5E5E5; ';
                                                    $__ABE__ .= 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#EFEFEF, endColorstr=#BCBCBC); ';
                                                    $__ABE__ .= 'background-image: -webkit-gradient(linear, left top, left bottom, from(#EFEFEF), #BCBCBC); ';
                                                    $__ABE__ .= 'background-image: -webkit-linear-gradient(top, #EFEFEF, #BCBCBC); ';
                                                    $__ABE__ .= 'background-image: -moz-linear-gradient(top, #EFEFEF, #BCBCBC); background-image: -ms-linear-gradient(top, #EFEFEF, #BCBCBC); ';
                                                    $__ABE__ .= 'background-image: -o-linear-gradient(top, #EFEFEF, #BCBCBC); border: 1px solid #AAAAAA; ';
                                                    $__ABE__ .= 'border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); ';
                                                    $__ABE__ .= 'font-family: Arial,serif; font-size: 9pt; font-weight: 400; line-height: 14px; margin: 2px 0; padding: 2px 7px; ';
                                                    $__ABE__ .= 'text-decoration: none; text-transform: capitalize;" href="' .urldecode(urlencode($link)). '?ACTION='.G::encrypt('processABE', URL_KEY).'&APP_UID=';
                                                    $__ABE__ .= G::encrypt($data->APP_UID, URL_KEY) . '&DEL_INDEX=' . G::encrypt($data->DEL_INDEX, URL_KEY);
                                                    $__ABE__ .= '&FIELD=' . G::encrypt($actionField, URL_KEY) . '&VALUE=' . G::encrypt($optValue, URL_KEY);
                                                    $__ABE__ .= '&ABER=' . G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY) . '" target="_blank" >' . $optName;
                                                    $__ABE__ .= '</a></td>' . (($index % 5 == 0) ? '</tr><tr>' : '  ');
                                                    $index++;
                                                }

                                                $__ABE__.='</tr></table></td>';
                                                break;
                                            case 'yesno':
                                                $__ABE__ .= '<td align="center"><a href="' . $link . 'dataField?APP_UID=' . urlencode(G::encrypt($data->APP_UID, URL_KEY)) . '&DEL_INDEX=' . urlencode(G::encrypt($data->DEL_INDEX, URL_KEY)). '&FIELD=' . urlencode(G::encrypt($actionField, URL_KEY)) . '&VALUE=' . urlencode(G::encrypt(1, URL_KEY)) . '&ABER=' . urlencode(G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY)) . '" target="_blank">' . G::LoadTranslation('ID_YES_VALUE') . '</a></td>';
                                                $__ABE__ .= '<td align="center"><a href="' . $link . 'dataField?APP_UID=' . urlencode(G::encrypt($data->APP_UID, URL_KEY)) . '&DEL_INDEX=' . urlencode(G::encrypt($data->DEL_INDEX, URL_KEY)) . '&FIELD=' . urlencode(G::encrypt($actionField, URL_KEY)) . '&VALUE=' . urlencode(G::encrypt(0, URL_KEY)) . '&ABER=' . urlencode(G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY)) . '" target="_blank">' . G::LoadTranslation('ID_NO_VALUE') . '</a></td>';
                                                break;
                                            case 'checkbox':
                                                $__ABE__ .= '<td align="center"><a href="' . $link . 'dataField?APP_UID=' . G::encrypt($data->APP_UID, URL_KEY) . '&DEL_INDEX=' . G::encrypt($data->DEL_INDEX, URL_KEY) . '&FIELD=' . G::encrypt($actionField, URL_KEY) . '&VALUE=' . G::encrypt($field->value, URL_KEY) . '&ABER=' . G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY) . '" target="_blank">Check</a></td>';
                                                $__ABE__ .= '<td align="center"><a href="' . $link . 'dataField?APP_UID=' . G::encrypt($data->APP_UID, URL_KEY) . '&DEL_INDEX=' . G::encrypt($data->DEL_INDEX, URL_KEY) . '&FIELD=' . G::encrypt($actionField, URL_KEY) . '&VALUE=' . G::encrypt($field->value, URL_KEY) . '&ABER=' . G::encrypt($abeRequest['ABE_REQ_UID'], URL_KEY) . '" target="_blank">Uncheck</a></td>';
                                                break;
                                        }
                                        $__ABE__ .= '</tr></table>';
                                    break;
                            }

                            $__ABE__ = preg_replace('/\<img src=\"\/js\/maborak\/core\/images\/(.+?)\>/', '' , $__ABE__);
                            $__ABE__ = preg_replace('/\<input\b[^>]*\/>/', '' , $__ABE__);
                            $__ABE__ = preg_replace('/<select\b[^>]*>(.*?)<\/select>/is', "", $__ABE__);
                            $__ABE__ = preg_replace('/align=\"center\"/', '' , $__ABE__);
                            $__ABE__ = preg_replace('/class="tableGrid_view" /', 'class="tableGrid_view" width="100%" ', $__ABE__);
                            $caseFields['APP_DATA']['__ABE__'] = $__ABE__;

                            G::LoadClass("Users");

                            $user = new Users();
                            $userDetails = $user->loadDetails($data->PREVIOUS_USR_UID);

                            if($configuration['ABE_MAILSERVER_OR_MAILCURRENT'] == 1 && $configuration['ABE_TYPE'] !== ''){
                                $emailFrom = ($userDetails["USR_FULLNAME"] . ' <' . $userDetails["USR_EMAIL"] . '>');
                            }else{                          
                                if(isset($emailSetup["MESS_FROM_NAME"]) && isset($emailSetup["MESS_FROM_MAIL"] )){
                                    $emailFrom = ($emailSetup["MESS_FROM_NAME"] . ' <' . $emailSetup["MESS_FROM_MAIL"] . '>');
                                }else{
                                    $emailFrom = ((isset($emailSetup["MESS_FROM_NAME"])) ? $emailSetup["MESS_FROM_NAME"] : $emailSetup["MESS_FROM_MAIL"]);
                                }
                            }
                            
                            G::LoadClass('wsBase');

                            $wsBaseInstance = new wsBase();
                            $result = $wsBaseInstance->sendMessage($data->APP_UID,
                                                                   $emailFrom,
                                                                   $email,
                                                                   '',
                                                                   '',
                                                                   $subject,
                                                                   $configuration['ABE_TEMPLATE'],
                                                                   $caseFields['APP_DATA'],
                                                                   '');
                            $abeRequest['ABE_REQ_STATUS'] = ($result->status_code == 0 ? 'SENT' : 'ERROR');

                            $body = '';
                            $messageSent = executeQuery('SELECT `APP_MSG_BODY` FROM `APP_MESSAGE` ORDER BY `APP_MSG_SEND_DATE` DESC LIMIT 1');

                            if (!empty($messageSent) && is_array($messageSent)) {
                                $body = $messageSent[1]['APP_MSG_BODY'];
                            }

                            $abeRequest['ABE_REQ_BODY'] = $body;

                            // Update 
                            try {
                                $abeRequestsInstance = new AbeRequests();
                                $abeRequestsInstance->createOrUpdate($abeRequest);
                            } catch (Exception $error) {
                                throw $error;
                            }
                        }
                    }
                }
            }
        } catch (Exception $error) {
            throw $error;
        }
    }
}

