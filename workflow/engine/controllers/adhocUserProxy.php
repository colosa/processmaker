<?php
require_once 'classes/model/Application.php';
G::LoadClass( 'case' );

class adhocUserProxy extends HttpProxyController
{
    //list of users into adhoc option
    function adhocAssignUsersk ($params)
    {

        G::LoadClass( 'groups' );
        G::LoadClass( 'tasks' );
        $oTasks = new Tasks();
        $aAux = $oTasks->getGroupsOfTask( $_SESSION['TASK'], 2 );
        $aAdhocUsers = array ();
        $oGroups = new Groups();
        foreach ($aAux as $aGroup) {
            $aUsers = $oGroups->getUsersOfGroup( $aGroup['GRP_UID'] );
            foreach ($aUsers as $aUser) {
                if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                    $aAdhocUsers[] = $aUser['USR_UID'];
                }
            }
        }
        $aAux = $oTasks->getUsersOfTask( $_SESSION['TASK'], 2 );
        foreach ($aAux as $aUser) {
            if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
                $aAdhocUsers[] = $aUser['USR_UID'];
            }
        }
        require_once 'classes/model/Users.php';
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( UsersPeer::USR_UID );
        $oCriteria->addSelectColumn( UsersPeer::USR_FIRSTNAME );
        $oCriteria->addSelectColumn( UsersPeer::USR_LASTNAME );
        $oCriteria->add( UsersPeer::USR_UID, $aAdhocUsers, Criteria::IN );
        $oDataset = UsersPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $aData = array ();
        while ($oDataset->next()) {
            $aData[] = $oDataset->getRow();
        }

        $this->data = $aData;

    }
    //assign user adhoc
    function reassignCase ($params)
    {
        $cases = new Cases();
        $cases->reassignCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], $_POST['USR_UID'], $_POST['THETYPE'] );
        $this->success = true;
    }
    //delete case adhoc
    function deleteCase ($params)
    {
        $ainfoCase = array ();
        try {
            $applicationUID = (isset( $_POST['APP_UID'] )) ? $_POST['APP_UID'] : $_SESSION['APPLICATION'];
            $app = new Application();
            $caseData = $app->load( $applicationUID );
            $data['APP_NUMBER'] = $caseData['APP_NUMBER'];

            $oCase = new Cases();
            $oCase->reportTableDeleteRecord($applicationUID);
            $oCase->removeCase( $applicationUID );

            $this->success = true;
            $this->msg = G::LoadTranslation( 'ID_CASE_DELETED_SUCCESSFULLY', SYS_LANG, $data );
        } catch (Exception $e) {
            $this->success = false;
            $this->msg = $e->getMessage();
        }
    }

}
//End adhocUserProxy
