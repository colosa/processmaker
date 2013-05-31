<?php
$response = new stdclass();
$response->status = isset($_SESSION['USER_LOGGED']);
if (isset($_REQUEST['dynaformEditorParams'])) {
    $_SESSION['Current_Dynafom']['Parameters'] = unserialize(stripslashes($_REQUEST['dynaformEditorParams']));
    if (isset($_REQUEST['DYN_UID'])) {
        $dynaform = new dynaform();
        $dynaform->load($_REQUEST['DYN_UID']);

        G::LoadClass('dynaformEditor');
        $editor = new dynaformEditor(array());
        $editor->file = $dynaform->getDynFilename();
        $editor->home = PATH_DYNAFORM;
        $editor->title = $dynaform->getDynTitle();
        $editor->dyn_uid = $dynaform->getDynUid();
        $editor->pro_uid = $dynaform->getProUid();
        $editor->dyn_type = $dynaform->getDynType();
        $editor->dyn_title = $dynaform->getDynTitle();
        $editor->dyn_description = $dynaform->getDynDescription();
        $editor->dyn_editor = 'processmap';
        $editor->_setUseTemporalCopy(true);

        $A = isset($_SESSION['Current_Dynafom']['Parameters']['URL']) ? $_SESSION['Current_Dynafom']['Parameters']['URL'] : '';
        $form = new Form($dynaform->getProUid() . '/' . $dynaform->getDynUid(), PATH_DYNAFORM, SYS_LANG, true);
        $properties = array('A' => $A, 'DYN_UID' => $dynaform->getDynUid(), 'PRO_UID' => $dynaform->getProUid(), 'DYN_TITLE' => $dynaform->getDynTitle(),
                            'DYN_TYPE' => $dynaform->getDynType(), 'DYN_DESCRIPTION' => $dynaform->getDynDescription(), 'WIDTH' => $form->width,
                            'MODE' => $form->mode, 'PRINTDYNAFORM' => $form->printdynaform, 'ADJUSTGRIDSWIDTH' => $form->adjustgridswidth,
                            'NEXTSTEPSAVE' => $form->nextstepsave);
        $tmp = $editor->_getTmpData();
        $tmp['Properties'] = $properties;
        $editor->_setTmpData($tmp);
    }
}
if (isset($_REQUEST['dynaformRestoreValues'])) {
    $aRetValues = unserialize(stripslashes($_REQUEST['dynaformRestoreValues']));
    if (isset($aRetValues['APPLICATION'])) {
        $_SESSION['APPLICATION'] = $aRetValues['APPLICATION'];
    }
    if (isset($aRetValues['PROCESS'])) {
        $_SESSION['PROCESS'] = $aRetValues['PROCESS'];
    }
    if (isset($aRetValues['TASK'])) {
        $_SESSION['TASK'] = $aRetValues['TASK'];
    }
    if (isset($aRetValues['INDEX'])) {
        $_SESSION['INDEX'] = $aRetValues['INDEX'];
    }
    if (isset($aRetValues['TRIGGER_DEBUG'])) {
        $_SESSION['TRIGGER_DEBUG'] = $aRetValues['TRIGGER_DEBUG'];
    }
    if (isset($aRetValues['APP_DATA'])) {
        $_SESSION['APP_DATA'] = $aRetValues['APP_DATA'];
    }
}
die(G::json_encode($response));