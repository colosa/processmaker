<?php

$ROL_UID = $_GET['ROL_UID'];
global $RBAC;
$oDataset = $RBAC->getAllUsers($ROL_UID);
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
            <td class="userGroupTitle">' . G::LoadTranslation('ID_ASSIGN_THE_ROLE') . ': '.$roleCode.'</td>
        </tr>
    </table>
</div>
<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
<div class="userGroupLink"><a href="#" onclick="backUsers(\''.$_GET['ROL_UID'].'\');return false;">' . G::LoadTranslation('ID_BACK_TO_USERS_LIST') . '</a></div>';

$tree->showSign = false;

$oDataset->next();
while ($aRow = $oDataset->getRow()) {
    $ID_ASSIGN = G::LoadTranslation('ID_ASSIGN');

    $user = '['.$aRow['USR_USERNAME'].'] '.$aRow['USR_FIRSTNAME'].' '.$aRow['USR_LASTNAME'];
    $USR_UID = $aRow['USR_UID'];

    $html = "
        <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
            <tr>
                <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$user}</td>
                <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href=\"javascript:assignUserToRole('{$ROL_UID}','{$USR_UID}');\">{$ID_ASSIGN}</a>]</td>
            </tr>
        </table>";

    $ch = &$tree->addChild('', $html, array('nodeType' => 'child'));
    $ch->point = '<img src="/images/users.png" />';

    $oDataset->next();
}

print($tree->render());
