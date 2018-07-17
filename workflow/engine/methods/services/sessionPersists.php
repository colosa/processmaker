<?php
$response = new stdclass();
$response->status = isset($_SESSION['USER_LOGGED']);
if (isset($_REQUEST['dynaformEditorParams'])) {
    $arrayParameterAux = @unserialize(rawurldecode($_REQUEST["dynaformEditorParams"]));

    if (! empty($arrayParameterAux) && isset($arrayParameterAux["DYNAFORM_NAME"])) {
        $arrayParameterAux["DYNAFORM_NAME"] = base64_decode($arrayParameterAux["DYNAFORM_NAME"]);
        $_SESSION["Current_Dynafom"]["Parameters"] = $arrayParameterAux;

        if (isset($_REQUEST['DYN_UID'])) {
            if (class_exists('Dynaform')) {
                require_once 'classes/model/Dynaform.php';
            }
            $dynaform = new Dynaform();
            $dynaform->load($_REQUEST['DYN_UID']);

            $editor = new DynaformEditor(array());
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
}
if (isset($_REQUEST['dynaformRestoreValues'])) {

    $aRetValues = unserialize(stripslashes(base64_decode($_REQUEST['dynaformRestoreValues'])));

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
}
die(G::json_encode($response));