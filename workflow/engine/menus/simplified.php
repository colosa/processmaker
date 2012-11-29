<?php
global $G_TMP_MENU;
global $RBAC;

$G_TMP_MENU->AddIdRawOption("S_HOME", "home/appList?t=todo", G::LoadTranslation("ID_HOME"),
    "/images/simplified/in-set-grey.png", null, null, null);

if ($RBAC->userCanAccess("PM_CASES") == 1) {
    $G_TMP_MENU->AddIdRawOption("S_DRAFT", "home/appList?t=draft", G::LoadTranslation("ID_DRAFT"),
        "/images/simplified/folder-grey.png", null, null, null);
    $G_TMP_MENU->AddIdRawOption("S_UNASSIGNED", "home/appList?t=unassigned", G::LoadTranslation("ID_UNASSIGNED"),
        "/images/simplified/folder-grey3.png", null, null, null);
    $G_TMP_MENU->AddIdRawOption("S_NEW_CASE", "#", G::LoadTranslation("ID_NEW_CASE"),
        "/images/simplified/plus-set-grey.png", null, null, null);
    $G_TMP_MENU->AddIdRawOption("S_ADVANCED_SEARCH", "home/appAdvancedSearch", G::LoadTranslation("ID_ADVANCEDSEARCH"),
        "/images/simplified/advancedSearch.png", null, null, null);
}

