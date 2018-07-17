<?php
/**
 * Method defined and copied from processmaker/workflow/engine/classes/class.configuration.php
 *
 */
function casesListDefaultFieldsAndConfig($action, $translation = 1)
{
    $caseColumns = array();
    $caseReaderFields = array();

    switch ($action) {
        case "draft":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "paused":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_THREAD_INDEX") : "**ID_THREAD_INDEX**", "dataIndex" => "APP_THREAD_INDEX", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DEL_INDEX") : "**ID_DEL_INDEX**", "dataIndex" => "DEL_INDEX", "width" => 80);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "APP_THREAD_INDEX");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "unassigned":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 40, "align" => "left");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
            //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_COMPLETED_BY_USER") : "**ID_COMPLETED_BY_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 110);
            //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_FINISH_DATE") : "**ID_FINISH_DATE**", "dataIndex" => "APP_FINISH_DATE", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "sent":
        case "participated":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "search":
        case "simple_search":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 100);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 120 );
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 120, "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DELEGATION_DATE") : "**ID_DELEGATION_DATE**", "dataIndex" => "DEL_DELEGATE_DATE", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 80);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_DELEGATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "to_revise":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50,"hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => false);
            //$caseColumns[] = array("header" => "Sent By", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            //$caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "to_reassign":
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => false);
            //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

            $caseReaderFields[] = array("name" => "TAS_UID");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            //$caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "gral":
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
        case "todo":
        default:
            //todo
            $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
            $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
            $caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);

            $caseReaderFields[] = array("name" => "APP_UID");
            $caseReaderFields[] = array("name" => "USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
            $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
            $caseReaderFields[] = array("name" => "DEL_INDEX");
            $caseReaderFields[] = array("name" => "APP_NUMBER");
            $caseReaderFields[] = array("name" => "APP_TITLE");
            $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
            $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
            $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
            $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
            $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
            $caseReaderFields[] = array("name" => "DEL_PRIORITY");
            $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
            $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
            $caseReaderFields[] = array("name" => "APP_STATUS");
            $caseReaderFields[] = array("name" => "CASE_SUMMARY");
            $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
            break;
    }

    return array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => 20, "dateformat" => "M d, Y");
}

function getDefaultConfig($action, $translation)
{
    $config = new Configurations();

    if (method_exists($config, "casesListDefaultFieldsAndConfig")) {
        $arrayConfig = $config->casesListDefaultFieldsAndConfig($action, $translation);
    } else {
        $arrayConfig = casesListDefaultFieldsAndConfig($action, $translation);
    }

    return $arrayConfig;
}

function getDefaultFields($action, $translation)
{
    $config = new Configurations();

    if (method_exists($config, "casesListDefaultFieldsAndConfig")) {
        $arrayConfig = $config->casesListDefaultFieldsAndConfig($action, $translation);
    } else {
        $arrayConfig = casesListDefaultFieldsAndConfig($action, $translation);
    }

    //Table APP_DELAY, fields
    $appDelayField = array(
        "APP_DELAY_UID",
        //"PRO_UID",
        //"APP_UID",
        "APP_THREAD_INDEX",
        "APP_DEL_INDEX",
        "APP_TYPE",
        //"APP_STATUS",
        "APP_NEXT_TASK",
        "APP_DELEGATION_USER",
        "APP_ENABLE_ACTION_USER",
        "APP_ENABLE_ACTION_DATE",
        "APP_DISABLE_ACTION_USER",
        "APP_DISABLE_ACTION_DATE",
        "APP_AUTOMATIC_DISABLED_DATE"
    );

    $arrayField = array();

    //Required fields for AppCacheView.php - addPMFieldsToCriteria()
    $arrayField[] = array("name" => "APP_UID", "fieldType" => "key", "label" => ($translation == 1)? G::LoadTranslation("ID_CASESLIST_APP_UID") : "**ID_CASESLIST_APP_UID**", "align" => "left", "width" => 80);
    $arrayField[] = array("name" => "DEL_INDEX", "fieldType" => "key" , "label" => ($translation == 1)? G::LoadTranslation("ID_CASESLIST_DEL_INDEX") : "**ID_CASESLIST_DEL_INDEX**", "align" => "left", "width" => 50);
    $arrayField[] = array("name" => "USR_UID", "fieldType" => "case field", "label" => ($translation == 1)? G::LoadTranslation("ID_CASESLIST_USR_UID") : "**ID_CASESLIST_USR_UID**", "align" => "left", "width" => 100);
    $arrayField[] = array("name" => "PREVIOUS_USR_UID", "fieldType" => "case field" , "label" => ($translation == 1)? G::LoadTranslation("ID_CASESLIST_PREVIOUS_USR_UID") : "**ID_CASESLIST_PREVIOUS_USR_UID**", "align" => "left", "width" => 100);

    if (count($arrayConfig["caseColumns"]) > 0) {
        foreach ($arrayConfig["caseColumns"] as $index1 => $value1) {
            if (!isset($value1["hidden"])) {
                $arrayAux1 = $value1;
                $arrayAux2 = array();

                foreach ($arrayAux1 as $index2 => $value2) {
                    $indexAux = $index2;

                    switch ($index2) {
                        case "dataIndex":
                            $indexAux = "name";
                            break;
                        case "header":
                            $indexAux = "label";
                            break;
                    }
                    $arrayAux2[$indexAux] = $value2;
                }

                $arrayAux2["fieldType"] = (!in_array($arrayAux2["name"], $appDelayField))? "case field" : "delay field";
                $arrayAux2["align"] = (isset($arrayAux2["align"]))? $arrayAux2["align"] : "left";
                $arrayAux2["width"] = (isset($arrayAux2["width"]))? intval($arrayAux2["width"]): 100;

                $arrayField[] = $arrayAux2;
            }
        }
    }

    $arrayField = calculateGridIndex($arrayField);

    return $arrayField;
}

function setAvailableFields($arrayAvailableField, $arrayField)
{
    $i = 0;
    $arrayFieldResult = array();

    foreach ($arrayAvailableField as $index1 => $value1) {
        $fieldType = "PM Table";

        foreach ($arrayField as $index2 => $value2) {
            if ($value2["name"] == $value1) {
                $fieldType = $value2["fieldType"];
                break;
            }
        }

        $arrayFieldResult[$i] = array("name" => $value1, "gridIndex" => $i, "fieldType" => $fieldType);
        $i = $i + 1;
    }

    return $arrayFieldResult;
}

function setCasesListFields($arrayCasesListField, $arrayField)
{
    $i = 0;
    $arrayFieldResult = array();

    foreach ($arrayCasesListField as $index1 => $value1) {
        $fieldName = $value1->name;

        $fieldTypeAux  = "PM Table";
        $fieldLabelAux = $fieldName;
        $fieldAlignAux = "left";
        $fieldWidthAux = 100;

        foreach ($arrayField as $index2 => $value2) {
            if ($value2["name"] == $fieldName) {
                $fieldTypeAux  = $value2["fieldType"];
                $fieldLabelAux = $value2["label"];
                $fieldAlignAux = $value2["align"];
                $fieldWidthAux = $value2["width"];
                break;
            }
        }

        $fieldType  = $fieldTypeAux;
        $fieldLabel = (isset($value1->label) && trim($value1->label) != "")? $value1->label : $fieldLabelAux;
        $fieldAlign = (isset($value1->align) && trim($value1->align) != "")? $value1->align : $fieldAlignAux;
        $fieldWidth = (isset($value1->width) && trim($value1->width) != "")? intval($value1->width) : $fieldWidthAux;

        $arrayFieldResult[$i] = array("name" => $fieldName, "gridIndex" => $i, "fieldType" => $fieldType, "label" => $fieldLabel, "align" => $fieldAlign, "width" => $fieldWidth);
        $i = $i + 1;
    }

    return $arrayFieldResult;
}

function fieldSet()
{
    global $conf;
    global $confCasesList;
    global $action;

    if (is_array($confCasesList)) {
        $validConfig = isset($confCasesList["first"]) && isset($confCasesList["second"]);
    } else {
        $validConfig = false;
    }

    if (!$validConfig) {
        $arrayField  = getDefaultFields($action, 0);
        $arrayConfig = getDefaultConfig($action, 0);

        $result = genericJsonResponse("", array(), $arrayField, $arrayConfig["rowsperpage"], $arrayConfig["dateformat"]);

        $conf->saveObject($result, "casesList", $action, "", "", "");

        echo G::json_encode($result);
    } else {
        echo G::json_encode($confCasesList);
    }
}

function fieldReset($translation)
{
    global $action;

    $arrayField  = getDefaultFields($action, $translation);
    $arrayConfig = getDefaultConfig($action, $translation);

    $result = genericJsonResponse("", array(), $arrayField, $arrayConfig["rowsperpage"], $arrayConfig["dateformat"]);

    echo G::json_encode($result);
}

function fieldComplete($translation)
{

    $filter = new InputFilter();
    global $action;

    $arrayField  = getDefaultFields($action, $translation);
    $arrayConfig = getDefaultConfig($action, $translation);

    //Get values from JSON request
    $first  = G::json_decode((isset($_POST["first"]))?  $_POST["first"] :  G::json_encode(array()));
    $first  = $filter->xssFilterHard($first);
    $second = G::json_decode((isset($_POST["second"]))? $_POST["second"] : G::json_encode(array()));
    $second = $filter->xssFilterHard($second);
    $pmtable = (isset($_POST["pmtable"]))? $_POST["pmtable"] : "";
    $pmtable = $filter->xssFilterHard($pmtable);
    $rowsperpage = (isset($_POST["rowsperpage"]))? $_POST["rowsperpage"] : $arrayConfig["rowsperpage"];
    $rowsperpage = $filter->xssFilterHard($rowsperpage);
    $dateformat  = (isset($_POST["dateformat"]) && !empty($_POST["dateformat"]))? $_POST["dateformat"] : $arrayConfig["dateformat"];
    $dateformat = $filter->xssFilterHard($dateformat);

    //Complete fields
    foreach ($first as $index1 => $value1) {
        $indexAux = 0;
        $sw = 0;

        foreach ($arrayField as $index2 => $value2) {
            if ($value2["name"] == $value1) {
                $indexAux = $index1;
                $sw = 1;
                break;
            }
        }

        if ($sw == 1) {
            unset($first[$indexAux]);
        }
    }

    foreach ($arrayField as $index1 => $value1) {
        $sw = 0;

        foreach ($second as $index2 => $value2) {
            if ($value2->name == $value1["name"]) {
                $sw = 1;
                break;
            }
        }

        if ($sw == 0) {
            $item = new stdClass();
            $item->name = $value1["name"];

            array_push($second, $item);
        }
    }

    $arrayNewFirst  = setAvailableFields($first, $arrayField);
    $arrayNewSecond = setCasesListFields($second, $arrayField);

    $result = genericJsonResponse($pmtable, $arrayNewFirst, $arrayNewSecond, $rowsperpage, $dateformat);

    echo G::json_encode($result);
}

function fieldLabelReset($translation)
{

    $filter = new InputFilter();
    global $action;

    $arrayField  = getDefaultFields($action, $translation);
    $arrayConfig = getDefaultConfig($action, $translation);

    //Get values from JSON request
    $first       = G::json_decode((isset($_POST["first"]))?  $_POST["first"] :  G::json_encode(array()));
    $first       = $filter->xssFilterHard($first);
    $second      = G::json_decode((isset($_POST["second"]))? $_POST["second"] : G::json_encode(array()));
    $second      = $filter->xssFilterHard($second);
    $pmtable     = (isset($_POST["pmtable"]))? $_POST["pmtable"] : "";
    $pmtable     = $filter->xssFilterHard($pmtable);
    $rowsperpage = (isset($_POST["rowsperpage"]))? $_POST["rowsperpage"] : $arrayConfig["rowsperpage"];
    $rowsperpage = $filter->xssFilterHard($rowsperpage);
    $dateformat  = (isset($_POST["dateformat"]) && !empty($_POST["dateformat"]))? $_POST["dateformat"] : $arrayConfig["dateformat"];
    $dateformat  = $filter->xssFilterHard($dateformat);

    //Reset label's fields
    foreach ($second as $index1 => $value1) {
        foreach ($arrayField as $index2 => $value2) {
            if ($value2["name"] == $value1->name) {
                $value1->label = $value2["label"];
                break;
            }
        }
    }

    $arrayNewFirst  = setAvailableFields($first, $arrayField);
    $arrayNewSecond = setCasesListFields($second, $arrayField);

    $result = genericJsonResponse($pmtable, $arrayNewFirst, $arrayNewSecond, $rowsperpage, $dateformat);

    echo G::json_encode($result);
}

function fieldSave()
{

    $filter = new InputFilter();
    global $conf;
    global $action;

    $arrayField  = getDefaultFields($action, 0);
    $arrayConfig = getDefaultConfig($action, 0);

    //Get values from JSON request
    $first       = G::json_decode((isset($_POST["first"]))?  $_POST["first"] :  G::json_encode(array()));
    $first       = $filter->xssFilterHard($first);
    $second      = G::json_decode((isset($_POST["second"]))? $_POST["second"] : G::json_encode(array()));
    $pmtable     = (isset($_POST["pmtable"]))? $_POST["pmtable"] : "";
    $pmtable     = $filter->xssFilterHard($pmtable);
    $rowsperpage = (isset($_POST["rowsperpage"]))? $_POST["rowsperpage"] : $arrayConfig["rowsperpage"];
    $rowsperpage = $filter->xssFilterHard($rowsperpage);
    $dateformat  = (isset($_POST["dateformat"]) && !empty($_POST["dateformat"]))? $_POST["dateformat"] : $arrayConfig["dateformat"];
    $dateformat  = $filter->xssFilterHard($dateformat);

    //Adding the key fields to second array
    //Required fields for AppCacheView.php - addPMFieldsToCriteria()
    $appUid = new stdClass();
    $appUid->name = "APP_UID";

    $delIndex = new stdClass();
    $delIndex->name = "DEL_INDEX";

    $usrUid = new stdClass();
    $usrUid->name = "USR_UID";

    $previousUsrUid = new stdClass();
    $previousUsrUid->name = "PREVIOUS_USR_UID";

    array_unshift($second, $previousUsrUid);
    array_unshift($second, $usrUid);
    array_unshift($second, $delIndex);
    array_unshift($second, $appUid);

    $arrayNewFirst  = setAvailableFields($first, $arrayField);
    $arrayNewSecond = setCasesListFields($second, $arrayField);

    $result = genericJsonResponse($pmtable, $arrayNewFirst, $arrayNewSecond, $rowsperpage, $dateformat);

    $conf->saveObject($result, "casesList", $action, "", "", "");

    $msgLog = '';
    
    if($action == 'todo') {
        $list = 'Inbox';
    } elseif ($action == 'sent') {
        $list = 'Participated';
    } else {
        $list = ucwords($action); 
    }

    for ($i=4; $i<count( $arrayNewSecond ); $i++) {
        if ($i == count( $arrayNewSecond )-1) {
            $msgLog .= $arrayNewSecond[$i]['label'];
        } else {
            $msgLog .= $arrayNewSecond[$i]['label'].'-';
        }
    }
    

    G::auditLog("SetColumns", "Set ".$list." List Columns".$msgLog);

    echo G::json_encode($result);
}

$callback = (isset($_POST["callback"]))? $_POST["callback"] : "stcCallback1001";
$dir  = (isset($_POST["dir"]))?  $_POST["dir"] : "DESC";
$sort = (isset($_POST["sort"]))? $_POST["sort"] : "";
$query  = (isset($_POST["query"]))?  $_POST["query"] : "";
$tabUid = (isset($_POST["table"]))?  $_POST["table"] : "";
$action = (isset($_POST["action"]))? $_POST["action"] : "todo";
$xaction = (isset($_POST["xaction"]))? $_POST["xaction"] : "FIELD_SAVE";

try {
    //Load the current configuration for this action, this configuration will be used later
    $conf = new Configurations();
    $confCasesList = $conf->loadObject("casesList", $action, "", "", "");

    switch ($xaction) {
        case "FIELD_SET":
            if (is_array($confCasesList) && isset($confCasesList['second']['data'])) {
                foreach ($confCasesList['second']['data'] as $key => $value) {
                    $confCasesList['second']['data'][$key]['align_label'] = $confCasesList['second']['data'][$key]['align'];
                }
            }
            fieldSet();
            break;
        case "FIELD_RESET":
        case "FIELD_RESET_ID":
            fieldReset(($xaction == "FIELD_RESET")? 1 : 0);
            break;
        case "FIELD_COMPLETE":
        case "FIELD_COMPLETE_ID":
            fieldComplete(($xaction == "FIELD_COMPLETE")? 1 : 0);
            break;
        case "FIELD_LABEL_RESET":
        case "FIELD_LABEL_RESET_ID":
            fieldLabelReset(($xaction == "FIELD_LABEL_RESET")? 1 : 0);
            break;
        case "FIELD_SAVE":
            fieldSave();
            break;
        case "getFieldsFromPMTable":
            xgetFieldsFromPMTable($tabUid);
            break;
    }
} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::json_encode( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) ) );
}

 /**
  * set the generic Json Response, using two array for the grid stores and a string for the pmtable name
  * @param string $pmtable
  * @param array $first
  * @param array $second
  * @return $response a json string
  */
function genericJsonResponse($pmtable, $first, $second, $rowsperpage, $dateFormat)
{
    $firstGrid['totalCount']  = count($first);
    $firstGrid['data']        = $first;
    $secondGrid['totalCount'] = count($second);
    $secondGrid['data']       = $second;
    $result = array();
    $result['first']   = $firstGrid;
    $result['second']  = $secondGrid;
    $result['PMTable'] = isset($pmtable) ? $pmtable : '';
    $result['rowsperpage'] = isset($rowsperpage) ? $rowsperpage : 20;
    $result['dateformat']  = isset($dateFormat) && $dateFormat != '' ? $dateFormat : 'M d, Y';
    return $result;
}

function xgetFieldsFromPMTable($tabUid)
{
    $rows = array();
    $result = array();
    //    $rows[] = array ( 'name' => 'val 1', 'gridIndex' => '21', 'fieldType' => 'PM Table' );
    //    $rows[] = array ( 'name' => 'val 2', 'gridIndex' => '22', 'fieldType' => 'PM Table' );
    //    $rows[] = array ( 'name' => 'val 3', 'gridIndex' => '23', 'fieldType' => 'PM Table' );
    //$result['success']    = true;
    //$result['totalCount']  =  count($rows);
    $oCriteria = new Criteria('workflow');
    $oCriteria->clearSelectColumns ( );
    $oCriteria->setDistinct();
    $oCriteria->addSelectColumn ( FieldsPeer::FLD_NAME );
    $oCriteria->addSelectColumn ( FieldsPeer::FLD_UID );
    $oCriteria->addSelectColumn ( FieldsPeer::FLD_INDEX );
    $oCriteria->add (FieldsPeer::ADD_TAB_UID, $tabUid , CRITERIA::EQUAL );
    $oCriteria->add (FieldsPeer::FLD_NAME, 'APP_UID' , CRITERIA::NOT_EQUAL );
    $oCriteria->addAnd (FieldsPeer::FLD_NAME, 'APP_NUMBER' , CRITERIA::NOT_EQUAL );
    $oCriteria->addDescendingOrderByColumn('FLD_INDEX');
    $oDataset = FieldsPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $index =  count($rows);

    while ($aRow = $oDataset->getRow()) {
        $aRow['index'] = ++$index;
        $aTempRow['name'] = $aRow['FLD_NAME'];
        $aTempRow['gridIndex'] = $aRow['index'];
        $aTempRow['fieldType'] = 'PM Table';
        $rows[] = $aTempRow;
        $oDataset->next();
    }

    $result['data']    = $rows;
    print G::json_encode( $result ) ;
}

 /**
  *
  * @param Array $fields
  * @return Array
  *
  */
function calculateGridIndex($fields)
{
    for ($i=0; $i<count( $fields ); $i++) {
        $fields[$i]['gridIndex']=$i+1;
    }
    return ($fields);
}

