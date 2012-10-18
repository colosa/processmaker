<?php

if (! isset( $_REQUEST['action'] )) {
    $return['success'] = 'failure';
    $return['message'] = 'You may request an action';
    print G::json_encode( $return );
    die();
}
if (! function_exists( $_REQUEST['action'] )) {
    $return['success'] = 'failure';
    $return['message'] = 'The requested action doesn\'t exists';
    print G::json_encode( $return );
    die();
}

$functionName = $_REQUEST['action'];
//var_dump($functionName);
$functionParams = isset( $_REQUEST['params'] ) ? $_REQUEST['params'] : array ();
$functionName( $functionParams );

function searchSavedJob ($schUid)
{

}

function pluginsList ()
{
    $oPluginRegistry = & PMPluginRegistry::getSingleton();
    $activePluginsForCaseScheduler = $oPluginRegistry->getCaseSchedulerPlugins();
    $selectedPlugin = "";
    if ((isset( $_REQUEST['plg_uid'] )) && ($_REQUEST['plg_uid'] != ""))
        $selectedPlugin = $_REQUEST['plg_uid'];
    if (! empty( $activePluginsForCaseScheduler )) {
        echo '<select style="width: 300px;" name="form[CASE_SH_PLUGIN_UID]" id="form[CASE_SH_PLUGIN_UID]" class="module_app_input___gray" required="1" onChange="showPluginSelection(this.options[this.selectedIndex].value,getField(\'PRO_UID\').value)">';
        echo "<option value=\"\">- Select -</option>";
        foreach ($activePluginsForCaseScheduler as $key => $caseSchedulerPluginDetail) {
            $sActionId = $caseSchedulerPluginDetail->sActionId;
            $sNamespace = $caseSchedulerPluginDetail->sNamespace;
            $optionId = $sNamespace . "--" . $sActionId;
            $selectedOption = "";
            if ($selectedPlugin == $optionId)
                $selectedOption = "selected";
            echo "<option value=\"$optionId\" $selectedOption>" . $sActionId . "</option>";
        }
        echo '</select>';
        //G::pr($activePlugnsForCaseScheduler);
    }
}

function pluginCaseSchedulerForm ()
{
    if (! isset( $_REQUEST['selectedOption'] ))
        die();
    $G_PUBLISH = new Publisher();
    $params = explode( "--", $_REQUEST['selectedOption'] );
    $oPluginRegistry = & PMPluginRegistry::getSingleton();
    $activePluginsForCaseScheduler = $oPluginRegistry->getCaseSchedulerPlugins();

    foreach ($activePluginsForCaseScheduler as $key => $caseSchedulerPluginDetail) {
        if (($caseSchedulerPluginDetail->sNamespace == $params[0]) && ($caseSchedulerPluginDetail->sActionId == $params[1])) {
            $caseSchedulerSelected = $caseSchedulerPluginDetail;
        }
    }
    if ((isset( $caseSchedulerSelected )) && (is_object( $caseSchedulerSelected ))) {
        //Render the form
        if ((isset( $_REQUEST['sch_uid'] )) && ($_REQUEST['sch_uid'] != "")) {
            //$oData=$oPluginRegistry->executeMethod( $caseSchedulerPluginDetail->sNamespace, $caseSchedulerPluginDetail->sActionGetFields, array("SCH_UID"=>$_REQUEST['sch_uid']) );
            $oData = array ("SCH_UID" => $_REQUEST['sch_uid'],"PRO_UID" => $_REQUEST['pro_uid']
            );
        } else {
            $oData = array ("PRO_UID" => $_REQUEST['pro_uid']
            );
        }
        $oPluginRegistry->executeMethod( $caseSchedulerPluginDetail->sNamespace, $caseSchedulerPluginDetail->sActionForm, $oData );
    }
}

