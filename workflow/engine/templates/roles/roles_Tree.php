<?php

$ROL_UID = $_GET['ROL_UID'];
global $RBAC;
$oDataset = $RBAC->getRoleUsers($ROL_UID);
$roleCode = $RBAC->getRoleCode($ROL_UID);

$tree = new PmTree();
$tree->name = 'Users';
$tree->nodeType = "base";
$tree->width = "350px";
$tree->value = '
<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
<div class="boxContentBlue">
    <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
        <tr>
            <td class="userGroupTitle">' . G::LoadTranslation('ID_USER_WITH_ROLE') . ': '.$roleCode.'</td>
        </tr>
    </table>
</div>
<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
<div class="userGroupLink"><a href="#" onclick="showUsersLoad(\''.$_GET['ROL_UID'].'\');return false;">'.G::LoadTranslation('ID_ASSIGN_ROLE').'</a></div>';

$tree->showSign = false;

$oDataset->next();
while ($aRow = $oDataset->getRow()) {
    $ID_DELETE = G::LoadTranslation('ID_REMOVE');
    $un = ($aRow['USR_USERNAME'] != '')?$aRow['USR_USERNAME']:'none';
    $user = '['.$un.'] '.$aRow['USR_FIRSTNAME'].' '.$aRow['USR_LASTNAME'];
    $USR_UID = $aRow['USR_UID'];

    if ($USR_UID != "00000000000000000000000000000001") { #because the admin remove rol it doesn't posible
        $refer = "<a href=\"javascript:deleteUserRole('{$ROL_UID}','{$USR_UID}');\">{$ID_DELETE}</a>";
    } else {
        $refer = "<font color='#CFCFCF'>{$ID_DELETE}</font>";
    }

    $html = "
        <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
            <tr>
                <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
                <td class='treeNode' style='border:0px;background-color:transparent;'>[$refer]</td>
            </tr>
        </table>";

    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';

    $oDataset->next();
}


print($tree->render());
