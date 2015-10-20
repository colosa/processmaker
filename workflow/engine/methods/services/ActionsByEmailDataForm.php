<?php

if (PMLicensedFeatures
        ::getSingleton()
        ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    
    try {
        // Validations
        if (!isset($_REQUEST['APP_UID'])) {
            $_REQUEST['APP_UID'] = '';
        }
    
        if (!isset($_REQUEST['DEL_INDEX'])) {
            $_REQUEST['DEL_INDEX'] = '';
        }
    
        if ($_REQUEST['APP_UID'] == '') {
            throw new Exception('The parameter APP_UID is empty.');
        }
    
        if ($_REQUEST['DEL_INDEX'] == '') {
            throw new Exception('The parameter DEL_INDEX is empty.');
        }
    
        G::LoadClass('case');
        G::LoadClass('pmDynaform');
    
        $cases = new Cases();
        $caseFields = $cases->loadCase(G::decrypt($_REQUEST['APP_UID'], URL_KEY),G::decrypt($_REQUEST['DEL_INDEX'], URL_KEY));
    
        //
        $criteria = new Criteria();
        $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $criteria->addSelectColumn(DynaformPeer::PRO_UID);
        $criteria->add(DynaformPeer::DYN_UID, G::decrypt($_REQUEST['DYN_UID'], URL_KEY));
        $result = DynaformPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $configuration = $result->getRow();
        
        $action = 'ActionsByEmailDataFormPost.php?APP_UID=' . $_REQUEST['APP_UID'] . '&DEL_INDEX=' . $_REQUEST['DEL_INDEX'] . '&ABER=' . $_REQUEST['ABER'];
        $record['DYN_CONTENT'] = $configuration['DYN_CONTENT'];
        $record['PRO_UID']     = $configuration['PRO_UID'];
        $record['CURRENT_DYNAFORM'] = G::decrypt($_REQUEST['DYN_UID'], URL_KEY);
        $record['APP_UID'] = $_REQUEST['APP_UID'];
        $record['DEL_INDEX'] = $_REQUEST['DEL_INDEX'];
        $record['ABER'] = $_REQUEST['ABER'];
        if (is_null($caseFields['DEL_FINISH_DATE'])) {
            $a = new pmDynaform($record);
            $a->printABE($action,$record);
        } else {
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', array('MESSAGE' => '<strong>The form has already been filled and sent.</strong>'));
        }
    } catch (Exception $error) {
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', array('MESSAGE' => $error->getMessage()));
    }
    
    G::RenderPage('publish', 'blank');
}

