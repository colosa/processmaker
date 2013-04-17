<?php
if (! isset ($_SESSION ['USER_LOGGED'])) {
    $res ['success'] = false;
    $res ['error'] = G::LoadTranslation('ID_LOGIN_AGAIN');
    $res ['login'] = true;
    print G::json_encode ($res);
    die ();
}
if (! isset ($_REQUEST ['action'])) {
    $res ['success'] = false;
    $res ['message'] = 'You may request an action';
    print G::json_encode ($res);
    die ();
}
if (! function_exists ($_REQUEST ['action'])) {
    $res ['success'] = false;
    $res ['message'] = 'The requested action does not exist';
    print G::json_encode ($res);
    die ();
}

if (($_REQUEST['action']) != 'rename') {
    $functionName = $_REQUEST ['action'];
    $functionParams = isset ($_REQUEST ['params']) ? $_REQUEST ['params'] : array ();

    $functionName ($functionParams);
} else {
    $functionName = 'renameFolder';
    $functionParams = isset ($_REQUEST ['params']) ? $_REQUEST ['params'] : array ();
    $oldname = $_REQUEST ['item'];
    $newname = $_REQUEST ['newitemname'];
    $oUid = $_REQUEST ['selitems'];

    if (isset($oUid[0])) {
        $uid = $oUid[0];
    } else {
        $uid = $oUid;
    }

    renameFolder ($oldname, $newname, $uid);
}

/////////////////////////////////////////////

function renameFolder($oldname, $newname, $uid)
{
    $folder = new AppFolder();
    //Clean Folder name (delete spaces...)
    $newname = trim( $newname );

    $fields = array();

    $fields['FOLDER_UID'] = $uid;
    $fields['FOLDER_NAME'] = $newname;
    $fields['FOLDER_UPDATE_DATE'] = date('Y-m-d H:i:s');

    $folder->update($fields);

    $msgLabel= G::LoadTranslation ('ID_EDIT_SUCCESSFULLY');
    echo "{action: '', error:'error',message: '$msgLabel', success: 'success',folderUID: 'root'}";
}

/**
 * delete folders and documents
 * created by carlos pacha carlos@colosa.com, pckrlos@gmail.com
 * @param void
 * @return true
**/
function delete()
{
    include_once ("classes/model/AppDocument.php");
    include_once ("classes/model/AppFolder.php");

    switch ($_REQUEST['option']) {
        case 'documents':
            deleteDocuments($_REQUEST['selitems'], $_REQUEST['option']);
            break;
        case 'directory':
            $oAppFoder    = new AppFolder ();
            $oAppDocument = new AppDocument ();
            $aDocuments   = $oAppDocument->getDocumentsinFolders($_REQUEST['item']);

            if (count($aDocuments) > 0) {
                deleteDocuments($aDocuments, $_REQUEST['option']);
            }

            $oAppFoder->remove($_REQUEST['item'],'');
            break;
    }
    $msgLabel= G::LoadTranslation ('ID_DELETED_SUCCESSFULLY');
    echo "{action: '', error:'error',message: '$msgLabel', success: 'success',folderUID: 'root'}";
}

/**
 * delete docuements
 * created by carlos pacha carlos@colosa.com, pckrlos@gmail.com
 * @param array $aDocuments
 * @param string $opt
 * @return true
**/
function deleteDocuments($aDocuments, $opt)
{
    include_once ("classes/model/AppDocument.php");
    $oAppDocument = new AppDocument ();
    foreach ($aDocuments as $key => $val) {
        if ($opt == 'documents') {
            list($sFileUID,$docVersion) = explode('_',$val);
        } else {
            $sFileUID   = $val['sAppDocUid'];
            $docVersion = $val['iVersion'];
        }
        $oAppDocument->remove($sFileUID,$docVersion);
    }
    return true;
}
/////////////////////////////////////////////
function getExtJSParams()
{
    $validParams = array('callback' => '', 'dir' => 'DESC', 'sort' => '', 'start' => 0, 'limit' => 25, 'filter' => '',
        'search' => '', 'action' => '', 'xaction' => '', 'data' => '', 'status' => '', 'query' => '', 'fields' => "");
    $result = array();
    foreach ($validParams as $paramName => $paramDefault) {
        $result[$paramName] = isset($_REQUEST[$paramName]) ?
        $_REQUEST[$paramName] : isset($_REQUEST[$paramName]) ? $_REQUEST[$paramName] : $paramDefault;
    }
    return $result;
}

function sendJsonResultGeneric($response, $callback)
{
    header("Content-Type: application/json");
    $finalResponse = G::json_encode($response);
    if ($callback != '') {
        print $callback . "($finalResponse);";
    } else {
        print $finalResponse;
    }
}

function expandNode()
{
    //require_once ("classes/model/AppFolder.php");

    extract(getExtJSParams());

    $oPMFolder = new AppFolder();

    $rootFolder = "/";

    if ($_POST ['node']=="") {
        $_POST ['node'] ="/";
    }

    if ($_POST ['node']=="root") {
        $_POST ['node'] ="/";
    }

    if (!(isset($_POST['sendWhat']))) {
        $_POST['sendWhat']="both";
    }

    if (isset($_POST['renderTree'])) {
        $limit = 1000000;
    }

    $totalItems=0;
    $totalFolders=0;
    $totalDocuments=0;

    if (($_POST['sendWhat'] == "dirs") || ($_POST['sendWhat'] == "both")) {
        $folderListObj = $oPMFolder->getFolderList(
            ($_POST["node"] != "root")? (($_POST["node"] == "NA")? "" : $_POST["node"]) : $rootFolder,
            $limit,
            $start
        );

        $folderList=$folderListObj['folders'];
        $totalFolders=$folderListObj['totalFoldersCount'];
        $totalItems+=count($folderList);
    }

    if (($_POST['sendWhat'] == "files") || ($_POST['sendWhat'] == "both")) {
        global $RBAC;

        $user = ($RBAC->userCanAccess('PM_ALLCASES') == 1)? '' : $_SESSION['USER_LOGGED'];

        $folderContentObj = $oPMFolder->getFolderContent(
            ($_POST["node"] != "root")? (($_POST["node"] == "NA")? "" : $_POST["node"]) : $rootFolder,
            array(),
            null,
            null,
            $limit,
            $start,
            $user,
            true
        );

        $folderContent=$folderContentObj['documents'];
        $totalDocuments=$folderContentObj['totalDocumentsCount'];
        $totalItems+=count($folderContent);
    }

    $processListTree = array();
    $tempTree = array();

    if (isset($folderList) && sizeof($folderList)>0) {
        //$tempTree=array();
        foreach ($folderList as $key => $obj) {
            //$tempTree ['all-obj'] = $obj;
            $tempTree ['text'] = $obj['FOLDER_NAME'];
            $tempTree ['id'] = $obj['FOLDER_UID'];
            $tempTree ['folderID'] = $obj['FOLDER_UID'];
            $tempTree ['cls'] = 'folder';
            $tempTree ['draggable'] = true;
            $tempTree ['name'] = $obj['FOLDER_NAME'];
            $tempTree ['type'] = "Directory";
            $tempTree ['is_file'] = false;
            $tempTree ['appDocCreateDate'] = $obj['FOLDER_CREATE_DATE'];
            $tempTree ['qtip'] ='<strong>Directory: </strong>'.$obj['FOLDER_NAME'].
                '<br /><strong>Create Date:</strong> '.$obj['FOLDER_CREATE_DATE'].'';
            $tempTree ['is_writable'] =true;
            $tempTree ['is_chmodable'] =true;
            $tempTree ['is_readable'] =true;
            $tempTree ['is_deletable'] =true;

            if ((isset($_POST['option']) )&& ($_POST['option'] == "gridDocuments")) {
                $tempTree ['icon'] = "/images/documents/extension/folder.png";
            }
            //$tempTree ['leaf'] = true;
            //$tempTree ['optionType'] = "category";
            //$tempTree['allowDrop']=false;
            //$tempTree ['singleClickExpand'] = false;
            /*
            if ($key != "No Category") {
                $tempTree ['expanded'] = true;
            } else {
                //$tempTree ['expanded'] = false;
                $tempTree ['expanded'] = true;
            }
            */
            $processListTree [] = $tempTree;
            $tempTree=array();
        }
        /*if ($_POST ['node'] == '/') {
            $notInFolderLabel = G::LoadTranslation ('ID_NOT_IN_FOLDER');
            $tempTree ['text'] = $notInFolderLabel;
            $tempTree ['id'] = "NA";
            $tempTree ['folderID'] = "NA";
            $tempTree ['cls'] = 'folder';
            $tempTree ['draggable'  ] = true;
            $tempTree ['name'] = $notInFolderLabel;
            $tempTree ['type'] = "Directory";
            $tempTree ['is_file'] = false;
            $tempTree ['qtip'] ='<strong>Directory: </strong>'.$notInFolderLabel.'<br /><i>Unfiled Files</i> ';
            $tempTree ['is_writable'] =true;
            $tempTree ['is_chmodable'] =true;
            $tempTree ['is_readable'] =true;
            $tempTree ['is_deletable'] =true;

            if ((isset($_POST['option']))&&($_POST['option']=="gridDocuments")) {
                $tempTree ['icon'] = "/images/documents/extension/bz2.png";
            }*/
            //$tempTree ['leaf'] = true;
            //$tempTree ['optionType'] = "category";
            //$tempTree['allowDrop']=false;
            //$tempTree ['singleClickExpand'] = false;
            /*
            if ($key != "No Category") {
                $tempTree ['expanded'] = true;
            } else {
                //$tempTree ['expanded'] = false;
                $tempTree ['expanded'] = true;
            }
            */
            /*$processListTree [] = $tempTree;
            $tempTree=array();
        }*/
    } else {
        if ($_POST ['node'] == '/') {
            //$tempTree=array();
            //$processListTree [] = array();
        }
    }

    if (isset($folderContent)) {
        foreach ($folderContent as $key => $obj) {
            $mimeInformation = getMime($obj["APP_DOC_FILENAME"]);

            $tempTree["text"] = $obj["APP_DOC_FILENAME"];
            $tempTree["name"] = $obj["APP_DOC_FILENAME"];
            $tempTree["type"] = $mimeInformation["description"];
            $tempTree["icon"] = $mimeInformation["icon"];

            /*
            if (isset($obj['OUT_DOC_GENERATE'])) {
                if ($obj['OUT_DOC_GENERATE'] == "BOTH") {
                    $arrayType=array("PDF","DOC");
                } else {
                    $arrayType=array($obj['OUT_DOC_GENERATE']);
                }
                foreach ($arrayType as $keyType => $fileType) {
                    $tempTree ['text'.$fileType] = $obj['APP_DOC_FILENAME'].".".strtolower($fileType);
                    $tempTree ['name'.$fileType] = $obj['APP_DOC_FILENAME'].".".strtolower($fileType);
                    $mimeInformation=getMime($obj['APP_DOC_FILENAME'].".".strtolower($fileType));
                    $tempTree ['type'.$fileType] = $mimeInformation['description'];
                    $tempTree ['icon'.$fileType] = $mimeInformation['icon'];
                }
            }
            */

            $tempTree ['appdocid'] = $obj['APP_DOC_UID'];
            $tempTree ['id'] = $obj['APP_DOC_UID_VERSION'];
            $tempTree ['cls'] = 'file';
            //$tempTree ['draggable'] = true;
            $tempTree ['leaf'] = true;
            $tempTree ['is_file'] = true;
            //if ((isset($_POST['option']))&&($_POST['option']=="gridDocuments")) {
            //}
            $tempTree ['docVersion'] = $obj['DOC_VERSION'];
            $tempTree ['appUid'] = $obj['APP_UID'];
            $tempTree ['usrUid'] = $obj['USR_UID'];
            $tempTree ['appDocType'] = ucfirst(strtolower($obj['APP_DOC_TYPE']));
            $tempTree ['appDocCreateDate'] = $obj['APP_DOC_CREATE_DATE'];
            $tempTree ['appDocPlugin'] = $obj['APP_DOC_PLUGIN'];
            $tempTree ['appDocTags'] = $obj['APP_DOC_TAGS'];
            $tempTree ['appDocTitle'] = $obj['APP_DOC_TITLE'];
            $tempTree ['appDocComment'] = $tempTree ['qtip'] = $obj['APP_DOC_COMMENT'];
            $tempTree ['appDocFileName'] = $obj['APP_DOC_FILENAME'];
            if (isset($obj['APP_NUMBER'])) {
                $tempTree ['appLabel'] = sprintf("%s '%s' (%s)",$obj['APP_NUMBER'],$obj['APP_TITLE'],$obj['STATUS']);
            } else {
                $tempTree ['appLabel'] = "No case related";
            }
            $tempTree ['proTitle'] = $obj['PRO_TITLE'];
            $tempTree ['appDocVersionable'] = 0;
            if (isset($obj['OUT_DOC_VERSIONING'])) {
                $tempTree ['appDocVersionable'] = $obj['OUT_DOC_VERSIONING'];
            } elseif (isset($obj['INP_DOC_VERSIONING'])) {
                $tempTree ['appDocVersionable'] = $obj['INP_DOC_VERSIONING'];
            }
            if (isset($obj['USR_LASTNAME']) && isset($obj['USR_LASTNAME'])) {
                $tempTree ['owner'] = $obj['USR_USERNAME'];
                $tempTree ['owner_firstname'] = $obj['USR_FIRSTNAME'];
                $tempTree ['owner_lastname'] = $obj['USR_LASTNAME'];
            } else {
                $tempTree ['owner'] = $obj['USR_USERNAME'];
                $tempTree ['owner_firstname'] = "";
                $tempTree ['owner_lastname'] = "";
            }
            $tempTree ['deletelabel'] = $obj['DELETE_LABEL'];

            if ((isset($obj['DOWNLOAD_LABEL'])) && ($obj['DOWNLOAD_LABEL']!="")) {
                $labelgen=strtoupper(str_replace(".","",$obj['DOWNLOAD_LABEL']));
                $tempTree ['downloadLabel'.$labelgen] = $obj['DOWNLOAD_LABEL'];
                $tempTree ['downloadLink'.$labelgen] = $obj['DOWNLOAD_LINK'];
            }
            $tempTree ['downloadLabel'] = $obj['DOWNLOAD_LABEL'];
            $tempTree ['downloadLink'] = $obj['DOWNLOAD_LINK'];

            if ((isset($obj['DOWNLOAD_LABEL1'])) && ($obj['DOWNLOAD_LABEL1']!="")) {
                $labelgen=strtoupper(str_replace(".","",$obj['DOWNLOAD_LABEL1']));
                $tempTree ['downloadLabel'.$labelgen] = $obj['DOWNLOAD_LABEL1'];
                $tempTree ['downloadLink'.$labelgen] = $obj['DOWNLOAD_LINK1'];
            }
            $tempTree ['downloadLabel1'] = $obj['DOWNLOAD_LABEL1'];
            $tempTree ['downloadLink1'] = $obj['DOWNLOAD_LINK1'];

            $tempTree ['appDocUidVersion'] = $obj['APP_DOC_UID_VERSION'];

            $tempTree ['is_readable'] = true;
            $tempTree ['is_file'] = true;

            //$tempTree ['optionType'] = "category";
            //$tempTree['allowDrop']=false;
            //$tempTree ['singleClickExpand'] = true;
            /*
            if ($key != "No Category") {
                $tempTree ['expanded'] = true;
            } else {
                //$tempTree ['expanded'] = false;
                $tempTree ['expanded'] = true;
            }
            */

            $tempTree["outDocGenerate"] = "";

            if (isset($obj["OUT_DOC_GENERATE"])) {
                switch ($obj["OUT_DOC_GENERATE"]) {
                    case "PDF":
                    case "DOC":
                        $mimeInformation = getMime($obj["APP_DOC_FILENAME"] . "." . strtolower($obj["OUT_DOC_GENERATE"]));

                        $tempTree["text"] = $obj["APP_DOC_FILENAME"] . "." . strtolower($obj["OUT_DOC_GENERATE"]);
                        $tempTree["name"] = $obj["APP_DOC_FILENAME"] . "." . strtolower($obj["OUT_DOC_GENERATE"]);
                        $tempTree["type"] = $mimeInformation["description"];
                        $tempTree["icon"] = $mimeInformation["icon"];
                        $tempTree["appDocFileName"] = $tempTree["name"];

                        $tempTree["downloadLabel"] = $tempTree["downloadLabel" . $obj["OUT_DOC_GENERATE"]];
                        $tempTree["downloadLink"] = $tempTree["downloadLink" . $obj["OUT_DOC_GENERATE"]];

                        $tempTree["id"] = $tempTree["id"] . "_" . $obj["OUT_DOC_GENERATE"];

                        $processListTree[] = $tempTree;
                        break;
                    case "BOTH":
                        $strExpander = null;
                        $mimeInformation = getMime($obj["APP_DOC_FILENAME"] . ".pdf");
                        $strExpander = $strExpander . "<a href=\"javascript:;\" onclick=\"openActionDialog(this, 'download', 'pdf'); return false;\" style=\"color: #000000; text-decoration: none;\"><img src=\"/images/documents/extension/pdf.png\" style=\"margin-left: 25px; border: 0;\" alt=\"\" /> <b>" . $obj["APP_DOC_FILENAME"] . ".pdf</b> (" . $mimeInformation["description"] . ")</a>";
                        $strExpander = $strExpander . "<br />";
                        $mimeInformation = getMime($obj["APP_DOC_FILENAME"] . ".doc");
                        $strExpander = $strExpander . "<a href=\"javascript:;\" onclick=\"openActionDialog(this, 'download', 'doc'); return false;\" style=\"color: #000000; text-decoration: none;\"><img src=\"/images/documents/extension/doc.png\" style=\"margin-left: 25px; border: 0;\" alt=\"\" /> <b>" . $obj["APP_DOC_FILENAME"] . ".doc</b> (" . $mimeInformation["description"] . ")</a>";

                        $tempTree["outDocGenerate"] = $strExpander;

                        $tempTree["text"] = $obj["APP_DOC_FILENAME"];
                        $tempTree["name"] = $obj["APP_DOC_FILENAME"];
                        $tempTree["type"] = "";
                        $tempTree["icon"] = "/images/documents/extension/document.png";
                        $tempTree["appDocFileName"] = $tempTree["name"];

                        //$tempTree["downloadLabel"] = $obj["DOWNLOAD_LABEL"];
                        //$tempTree["downloadLink"] = $obj["DOWNLOAD_LINK"];

                        $tempTree["id"] = $tempTree["id"] . "_" . $obj["OUT_DOC_GENERATE"];

                        $processListTree[] = $tempTree;
                        break;
                    //case "NOFILE":
                    //    break;
                }
            } else {
                if ($obj["APP_DOC_TYPE"] == "OUTPUT" &&
                    $tempTree["type"] == G::LoadTranslation("MIME_DES_FILE") &&
                    preg_match("/^.+&ext=(.+)&.+$/", $tempTree["downloadLink"], $arrayMatch)
                ) {
                    $ext = $arrayMatch[1];
                    $mimeInformation = getMime($obj["APP_DOC_FILENAME"] . ".$ext");

                    $tempTree["text"] = $obj["APP_DOC_FILENAME"] . ".$ext";
                    $tempTree["name"] = $obj["APP_DOC_FILENAME"] . ".$ext";
                    $tempTree["type"] = $mimeInformation["description"];
                    $tempTree["icon"] = $mimeInformation["icon"];
                }

                $processListTree[] = $tempTree;
            }

            $tempTree = array();
        }
    }

    if ((isset($_POST['option'])) && ($_POST['option'] == "gridDocuments")) {
        $processListTreeTemp["totalCount"] = $totalFolders + $totalDocuments;
        $processListTreeTemp['msg']='correct reload';
        $processListTreeTemp['items']=$processListTree;
        $processListTree = $processListTreeTemp;
    }

    echo G::json_encode($processListTree);
}

function openPMFolder()
{
    $WIDTH_PANEL = 350;
    G::LoadClass ('tree');
    $folderContent = $oPMFolder->getFolderList ($_POST ['folderID'] != '0' ?
        $_POST ['folderID'] == 'NA' ? "" : $_POST ['folderID'] : $rootFolder);
    //krumo($folderContent);
    if (! is_array ($folderContent)) {
        echo $folderContent;
        exit ();
    }

    $tree = new Tree ();
    $tree->name = 'DMS';
    $tree->nodeType = "blank";

    //$tree->width="350px";
    $tree->value = '';
    $tree->showSign = false;

    $i = 0;
    foreach ($folderContent as $key => $obj) {
        $i ++;
        //if ($obj->item_type=="F") {

        $RowClass = ($i % 2 == 0) ? 'Row1' : 'Row2';
        $id_delete = G::LoadTranslation('ID_DELETE');
        $id_edit = G::LoadTranslation('ID_EDIT');

        $htmlGroup = <<<GHTML
        <table cellspacing='0' cellpadding='0' border='1' style='border:0px;' width="100%" class="pagedTable">
        <tr id="{$i}"  onmouseout="setRowClass(this, '{$RowClass}')" onmouseover="setRowClass(this, 'RowPointer')"
        class="{$RowClass}" style="cursor:hand">
        <td width='' class='treeNode' style='border:0px;background-color:transparent;'><a href="#"
        onclick="focusRow(this, 'Selected');openPMFolder('{$obj['FOLDER_UID']}','{$_POST['rootfolder']}');">
        <img src="/images/folderV2.gif" border = "0" valign="middle" />&nbsp;{$obj['FOLDER_NAME']}</a>
        <a href="#" onclick="deletePMFolder('{$obj['FOLDER_UID']}','{$_POST['rootfolder']}');">&nbsp; {$id_delete}</a>
        </td>
        </tr>
        </table>
        <div id="child_{$obj['FOLDER_UID']}"></div>
        GHTML;

        $ch = & $tree->addChild ($key, $htmlGroup, array ('nodeType' => 'child'));
        $ch->point = ' ';
        }
        $RowClass = ($i % 2 == 0) ? 'Row1' : 'Row2';
        $key = 0;
        if ($_POST ['folderID'] == '0') {
            $notInFolderLabel = G::LoadTranslation ('ID_NOT_IN_FOLDER');
            $htmlGroup = <<<GHTML
        <table cellspacing='0' cellpadding='0' border='1' style='border:0px;' width="100%" class="pagedTable">
        <tr id="{$i}" onclick="focusRow(this, 'Selected');openPMFolder('NA');"
        onmouseout="setRowClass(this, '{$RowClass}')" onmouseover="setRowClass(this, 'RowPointer')" class="{$RowClass}">
        <td width='' class='treeNode' style='border:0px;background-color:transparent;'><a href="#" onclick="">
        <img src="/images/folderV2.gif" border = "0" valign="middle" />&nbsp;- {$notInFolderLabel} -</a>&nbsp;</td>

        </tr>
        </table>
        <div id="child_NA"></div>
GHTML;

        $ch = & $tree->addChild ($key, $htmlGroup, array ('nodeType' => 'child'));
        $ch->point = ' ';
    }

    print ($tree->render ()) ;

}

function getPMFolderContent()
{
    $swSearch = false;

    if (isset ($_POST ['folderID'])) {
        //Render content of a folder
        $folderID = $_POST ['folderID'] != '0' ? $_POST ['folderID'] == 'NA' ? "" : $_POST ['folderID'] : $rootFolder;
        $folderContent = $oPMFolder->getFolderContent ($folderID);
    } else {
        // Perform a Search
        $swSearch = true;
        $folderContent = $oPMFolder->getFolderContent (null, array (), $_POST ['searchKeyword'], $_POST ['type']);
    }
    array_unshift ($folderContent, array ('id' => 'char'));
    if (! is_array ($folderContent)) {
        echo $folderContent;
        exit ();
    }

    $_DBArray ['PM_FOLDER_DOC'] = $folderContent;
    $_SESSION ['_DBArray'] = $_DBArray;

    G::LoadClass ('ArrayPeer');
    $c = new Criteria ('dbarray');
    $c->setDBArrayTable ('PM_FOLDER_DOC');
    $c->addAscendingOrderByColumn ('id');
    $G_PUBLISH = new Publisher ();
    require_once ('classes/class.xmlfield_InputPM.php');

    $labelFolderAddFile = "";
    $labelFolderAddFolder = "";
    if ($RBAC->userCanAccess ('PM_FOLDERS_ADD_FILE') == 1) {
        $labelFolderAddFile = G::LoadTranslation ('ID_ATTACH');
    }
    if ($RBAC->userCanAccess ('PM_FOLDERS_ADD_FOLDER') == 1) {
        $labelFolderAddFolder = G::LoadTranslation ('ID_NEW_FOLDER');
    }

    if (! $swSearch) {
        $G_PUBLISH->AddContent ('propeltable', 'paged-table', 'appFolder/appFolderDocumentList',
            $c, array ('folderID' => $_POST ['folderID'] != '0' ? $_POST ['folderID'] == 'NA' ?
            "/" : $_POST ['folderID'] : $rootFolder, 'labelFolderAddFile' => $labelFolderAddFile,
            'labelFolderAddFolder' => $labelFolderAddFolder));
        $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'appFolder/appFolderDocumentListHeader', '',
            array (), 'appFolderList?folderID=' . $_POST ['folderID']);
    } else {
        $G_PUBLISH->AddContent ('propeltable', 'paged-table', 'appFolder/appFolderDocumentListSearch', $c, array ());
        $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'appFolder/appFolderDocumentListHeader', '', array (),
            'appFolderList?folderID=/');
    }

    G::RenderPage ('publish', 'raw');
}

function getPMFolderTags()
{
    // Default font sizes
    $min_font_size = 12;
    $max_font_size = 30;

    $rootFolder = "/";
    $folderID = $_POST ['rootFolder'] != '0' ? $_POST ['rootFolder'] == 'NA' ? "" : $_POST ['rootFolder'] : $rootFolder;
    $tags = $oPMFolder->getFolderTags ($folderID);
    $minimum_count = 0;
    $maximum_count = 0;
    if ((is_array ($tags)) && (count ($tags) > 0)) {
        $minimum_count = min (array_values ($tags));
        $maximum_count = max (array_values ($tags));
    }
    $spread = $maximum_count - $minimum_count;

    if ($spread == 0) {
        $spread = 1;
    }

    $cloud_html = '';
    $cloud_tags = array (); // create an array to hold tag code
    foreach ($tags as $tag => $count) {
        $href = "#";
        //$href="?q="$tag;
        $size = $min_font_size + ($count - $minimum_count) * ($max_font_size - $min_font_size) / $spread;
        $cloud_tags [] = '<a style="font-size: ' . floor ($size) . 'px' . '" class="tag_cloud" href="' . $href .
            '" onClick="getPMFolderSearchResult(\'' . $tag . '\',\'TAG\')"' . ' title="\'' . $tag .
            '\' returned a count of ' . $count . '">' . htmlspecialchars (stripslashes ($tag)) . '</a>';
    }
    $cloud_html = join ("\n", $cloud_tags) . "\n";

    print "$cloud_html";

}

function uploadDocument()
{
    $uploadDocumentComponent=array();

    $uploadDocumentComponent["xtype"]= "tabpanel";
    $uploadDocumentComponent["stateId"]= "upload_tabpanel";
    $uploadDocumentComponent["activeTab"]= "uploadform";
    $uploadDocumentComponent["dialogtitle"]= G::LoadTranslation('ID_UPLOAD');
    $uploadDocumentComponent["stateful"]= true;

    $uploadDocumentComponent["stateEvents"]= array("tabchange");
    $uploadDocumentComponent["getState"]= "function_getState";
    $functionsToReplace['function_getState']="function() {
        return {
            activeTab:this.items.indexOf(this.getActiveTab())
        };
    }";
    $uploadDocumentComponent["listeners"]["resize"]["fn"]="function_listeners_resize";
    $functionsToReplace['function_listeners_resize'] = "function(panel) {
        panel.items.each(function(item) { item.setHeight(500);return true });
    }";
    $uploadDocumentComponent["items"]=array();

    $itemA=array();

    $itemA["xtype"]= "swfuploadpanel";
    $itemA["title"]= "flashupload";
    $itemA["height"]= "300";
    $itemA["id"]= "swfuploader";
    $itemA["viewConfig"]["forceFit"]=true;
    $itemA["listeners"]["allUploadsComplete"]["fn"]="function_listeners_allUploadsComplete";
    $functionsToReplace['function_listeners_allUploadsComplete'] = "function(panel) {
                                    datastore.reload();
                                    panel.destroy();
                                    Ext.getCmp('dialog').destroy();
                                    statusBarMessage('upload_completed', false, true);
                                }";

    // Uploader Params
    $itemA["upload_url"]= "../appFolder/appFolderAjax.php";
    $itemA["post_params"][session_name()]=session_id();
    $itemA["post_params"]["option"]="uploadFile";
    $itemA["post_params"]["action"]="upload";
    $itemA["post_params"]["dir"]="datastore.directory";
    $itemA["post_params"]["requestType"]="xmlhttprequest";
    $itemA["post_params"]["confirm"]="true";

    $itemA["flash_url"]="/scripts/extjs3-ext/ux.swfupload/swfupload.swf";
    $itemA["file_size_limit"]=get_max_file_size();
    // Custom Params
    $itemA["single_file_select"]=false; // Set to true if you only want to select one file from the FileDialog.
    $itemA["confirm_delete"]=false; // This will prompt for removing files from queue.
    $itemA["remove_completed"]=false; // Remove file from grid after uploaded.
    //$uploadDocumentComponent["items"][]=$itemA;

    //Standard Upload
    $itemA=array();
    $itemA["xtype"]="form";
    $itemA["autoScroll"]=true;
    $itemA["autoHeight"]=true;
    $itemA["id"]="uploadform";
    $itemA["fileUpload"]=true;
    $itemA["labelWidth"]="125";
    $itemA["url"]="URL_SCRIPT";
    $itemA["title"]=G::LoadTranslation('ID_UPLOAD');
    //$itemA["tooltip"]="Max File Size <strong>". ((get_max_file_size() / 1024) / 1024)." MB</strong><br />
    //Max Post Size<strong>". ((get_max_upload_limit() / 1024) / 1024)." MB</strong><br />";
    $itemA["frame"]=true;
    $itemA["items"]=array();
    $itemB=array();

    $itemB["xtype"]="displayfield";
    $itemB["value"]="Max File Size <strong>". ((get_max_file_size() / 1024) / 1024)." MB</strong><br />
    Max Post Size<strong>". ((get_max_upload_limit() / 1024) / 1024)." MB</strong><br />";
    //$itemA["items"][]=$itemB;

    for ($i=0; $i<7; $i++) {
        $itemB=array();

        $itemB["xtype"]="fileuploadfield";
        $itemB["fieldLabel"]="File ".($i+1);
        $itemB["id"]="uploadedFile[$i]";
        $itemB["name"]="uploadedFile[$i]";
        $itemB["width"]=275;
        $itemB["buttonOnly"]= false;
        $itemA["items"][]=$itemB;
    }

    $itemB=array();

    $itemB["xtype"]="checkbox";
    $itemB["fieldLabel"]="Overwrite";//G::LoadTranslation('ID_OVERWRITE');
    $itemB["name"]="overwrite_files";
    $itemB["checked"]=true;
    $itemA["items"][]=$itemB;

    $itemA["buttons"]=array();

    $buttonA=array();
    $buttonA["text"]=G::LoadTranslation('ID_SAVE');
    $buttonA["handler"]="function_standardupload_btnsave";
    $functionsToReplace["function_standardupload_btnsave"]=' function() {
                statusBarMessage("'.G::LoadTranslation('ID_UPLOADING_FILE').'", true, true);
                form = Ext.getCmp("uploadform").getForm();

                //Ext.getCmp("uploadform").getForm().submit();
                //console.log(form);
                //console.log(form.url);
                Ext.getCmp("uploadform").getForm().submit({
                    //reset: true,
                    reset: false,
                    success: function(form, action) {

                        datastore.reload();
                        statusBarMessage(action.result.message, false, true);
                        Ext.getCmp("dialog").destroy();
                    },
                    failure: function(form, action) {

                        if(!action.result) return;
                        Ext.MessageBox.alert("error", action.result.error);
                        statusBarMessage(action.result.error, false, false);
                    },
                    scope: Ext.getCmp("uploadform"),
                    // add some vars to the request, similar to hidden fields
                    params: {
                        option: "standardupload",
                        action: "uploadExternalDocument",
                        dir: datastore.directory,
                        requestType: "xmlhttprequest",
                        confirm: "true",
                        docUid: "-1",
                        appId: "00000000000000000000000000000000"
                    }
                });
            }';

    $itemA["buttons"][]=$buttonA;

    $buttonA=array();

    $buttonA["text"]= G::LoadTranslation('ID_CANCEL');
    $buttonA["handler"]="function_standardupload_btncancel";
    $functionsToReplace["function_standardupload_btncancel"]=' function() { Ext.getCmp("dialog").destroy(); }';
    $itemA["buttons"][]=$buttonA;

    $uploadDocumentComponent["items"][]=$itemA;

    $itemA=array();

    $itemA["xtype"]="form";
    $itemA["id"]="transferform";
    $itemA["url"]="../appFolder/appFolderAjax.php";
    $itemA["hidden"]="true";
    $itemA["title"]="acttransfer";
    $itemA["autoHeight"]="true";
    $itemA["labelWidth"]=225;
    $itemA["frame"]= true;
    $itemA["items"]=array();


    for ($i=0; $i<7; $i++) {
        $itemB=array();
        $itemB["xtype"]= "textfield";
        $itemB["fieldLabel"]= "url_to_file";
        $itemB["name"]= "userfile[$i]";
        $itemB["width"]=275;
        $itemA["items"][]=$itemB;
    }
    $itemB=array();
    $itemB["xtype"]="checkbox";
    $itemB["fieldLabel"]="overwrite_files";
    $itemB["name"]="overwrite_files";
    $itemB["checked"]=true;
    $itemA["items"][]=$itemB;

    $itemA["buttons"]=array();

    $buttonA=array();

    $buttonA["text"]="btnsave";
    $buttonA["handler"]="function_transfer_btnsave";
    $functionsToReplace["function_transfer_btnsave"]='function() {
                statusBarMessage("transfer_processing", true, true);
                transfer = Ext.getCmp("transferform").getForm();
                transfer.submit({
                    //reset: true,
                    reset: false,
                    success: function(form, action) {
                        datastore.reload();
                        statusBarMessage(action.result.message, false, true);
                        Ext.getCmp("dialog").destroy();
                    },
                    failure: function(form, action) {
                        if(!action.result) return;
                        Ext.MessageBox.alert("error", action.result.error);
                        statusBarMessage(action.result.error, false, false);
                    },
                    scope: transfer,
                    // add some vars to the request, similar to hidden fields
                    params: {
                        "option": "com_extplorer",
                        "action": "transfer",
                        "dir": datastore.directory,
                        "confirm": "true"
                    }
                });
            }';

    $itemA["buttons"]=$buttonA;

    $buttonA=array();
    $buttonA["text"]="btncancel";
    $buttonA["handler"]="function_transfer_btncancel";
    $functionsToReplace["function_transfer_btncancel"]='function() { Ext.getCmp("dialog").destroy(); }';
    $itemA["buttons"]=$buttonA;

    //         $uploadDocumentComponent["items"][]=$itemA;

    $finalResponse=G::json_encode($uploadDocumentComponent);
    $finalResponse=str_replace("URL_SCRIPT","../appFolder/appFolderAjax.php",$finalResponse);
    foreach ($functionsToReplace as $key => $originalFunction) {
        $finalResponse=str_replace('"'.$key.'"',$originalFunction,$finalResponse);
    }
    echo ($finalResponse);

    /*
     //krumo($_POST);
     G::LoadClass ('case');
     $oCase = new Cases ();

     $G_PUBLISH = new Publisher ();
     $Fields ['DOC_UID'] = $_POST ['docID'];
     $Fields ['APP_DOC_UID'] = $_POST ['appDocId'];
     $Fields ['actionType'] = $_POST ['actionType'];
     $Fields ['docVersion'] = $_POST ['docVersion'];

     $Fields ['appId'] = $_POST ['appId'];
     $Fields ['docType'] = $_POST ['docType'];
     $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'cases/cases_AttachInputDocumentGeneral', '', $Fields,
     'appFolderSaveDocument?UID=' . $_POST ['docID'] . '&appId=' . $_POST ['appId'] . '&docType=' .
     $_POST ['docType']);
     G::RenderPage ('publish', 'raw');
     */
}
function copyAction()
{
    copyMoveAction("copy");
}
function moveAction()
{
    copyMoveAction("move");
}

function findChilds($uidFolder, $path, $arrayPath) {
    $Criteria = new Criteria ();
    $Criteria->addSelectColumn ( AppFolderPeer::FOLDER_UID );
    $Criteria->addSelectColumn ( AppFolderPeer::FOLDER_PARENT_UID );
    $Criteria->addSelectColumn ( AppFolderPeer::FOLDER_NAME );
    $Criteria->addSelectColumn ( AppFolderPeer::FOLDER_CREATE_DATE );
    $Criteria->addSelectColumn ( AppFolderPeer::FOLDER_UPDATE_DATE );

    $Criteria->add(AppFolderPeer::FOLDER_PARENT_UID, $uidFolder);
    $Criteria->addAscendingOrderByColumn(AppFolderPeer::FOLDER_NAME);

    $rs = appFolderPeer::doSelectRS ( $Criteria );
    $rs->setFetchmode ( ResultSet::FETCHMODE_ASSOC );

    $folderResult = array ();
    $appFoder = new AppFolder ();
    while ($rs->next()) {
        $row = $rs->getRow();
        $path = ($uidFolder != '/')? $path : '';
        $path = $path."/".$row['FOLDER_NAME'];
        $arrayPath[] = array($row['FOLDER_UID'],$path);
        $arrayPath = findChilds($row['FOLDER_UID'], $path, $arrayPath);
    }
    return $arrayPath;
}
function copyMoveAction($type)
{
    require_once ("classes/model/AppFolder.php");
    $oPMFolder = new AppFolder ();

    $dir=$_REQUEST['dir'];
    $paths = array();
    $folderResult = findChilds('/', '', $paths);
    $withCombo = 30;
    foreach ($folderResult as $key => $value) {
        $count = strlen($value[1]);
        $withCombo = ($count>$withCombo) ? $count : $withCombo;
    }
    $root = array("/","/");
    array_unshift ($folderResult,$root);

    $dirCompletePath=$oPMFolder->getFolderStructure($dir);
    $copyDialog["xtype"]        = "form";
    $copyDialog["id"]           = "simpleform";
    $copyDialog["labelWidth"]   = 80;
    $copyDialog["width"]        = 500;
    $copyDialog["modal"]        = true;
    $copyDialog["url"]          = "URL_SCRIPT";
    if ($type=="copy") {
        $copyDialog["dialogtitle"]= "Copy";
    } else {
        $copyDialog["dialogtitle"]= "Move";
    }

    $copyDialog["frame"]= true;
    $copyDialog["items"]=array();

    $itemField=array();
    $itemField["xtype"]         = "combo";
    $itemField["hiddenName"]    = "new_dir";
    $itemField["id"]            = "new_dir_label";
    $itemField["name"]          = "new_dir_label";
    $itemField["mode"]          = "local";
    $itemField["triggerAction"] = "all";
    $itemField["store"]         = $folderResult;
    $itemField["valueField"]    = "FOLDER_UID";
    $itemField["editable"]    = false;
    $itemField["displayField"]  = "FOLDER_NAME";
    $itemField["selectOnFocus"] = true;
    $itemField["tpl"]           = '<tpl for="."><div ext:qtip="{field2}" class="x-combo-list-item">{field2}</div></tpl>';
    $itemField["fieldLabel"]    = G::LoadTranslation('ID_DESTINATION');
    $itemField["emptyText"]     = G::LoadTranslation('ID_SELECT_DIRECTORY');
    $itemField["width"] = 390;
    $itemField["allowBlank"]=false;
    $copyDialog["items"][]=$itemField;

    $itemField=array();
    $itemField["xtype"]="hidden";
    $itemField["fieldLabel"]="copyMove";
    $itemField["name"]="copyMove";
    $itemField["value"]="all";
    $itemField["width"]=175;
    $itemField["allowBlank"]=false;
    $copyDialog["items"][]=$itemField;

    $copyDialog["buttons"]=array();

    $itemButton=array();
    if ($type == "copy") {
        $itemButton["text"]= "Copy";
    } else {
        $itemButton["text"]= "Move";
    }
    $itemButton["handler"]="copyDialogCreateButtonFunction";
    $itemButton["id"]="buttonCopy";
    $functionsToReplace["copyDialogCreateButtonFunction"]="function() {
        form =  Ext.getCmp('simpleform').getForm();
        var requestParams = getRequestParams();
        requestParams.confirm = 'true';
        if (Ext.getCmp('new_dir_label').getValue() == '') {
            statusBarMessage('Select a Directory', false, false);
            return false;
        }
        requestParams.new_dir = Ext.getCmp('new_dir_label').getValue()
        statusBarMessage('Please wait...', true, true);
        Ext.getCmp('new_dir_label').disable();
        Ext.getCmp('buttonCopy').disable();
        Ext.getCmp('buttonCancel').disable();
        requestParams.action  = '".$type."Execute';
        form.submit({
            reset: false,
            success: function(form, action) {
                if(action.result.success){
                    if(action.result.success=='success'){
                        statusBarMessage(action.result.message, false, true);
                        var node = dirTree.getNodeById('root');
                        node.select();
                        datastore.directory = 'root';
                        datastore.reload();
                        dirTree.getRootNode().reload();
                        requestParams.dir = 'root';
                        Ext.getCmp('dialog').destroy();
                    }else{
                        statusBarMessage(action.result.message, false, false);
                    }
                }else{
                    if(!action.result) return;
                    Ext.MessageBox.alert('Error!', action.result.error);
                    statusBarMessage(action.result.error, false, false);
                }
            },
            failure: function(form, action) {
                if(!action.result) return;
                Ext.MessageBox.alert('Error!', action.result.error);
                statusBarMessage(action.result.error, false, false);
            },
            scope: form,
            // add some vars to the request, similar to hidden fields
            params: requestParams
        });
    }";
    $copyDialog["buttons"][]=$itemButton;

    $itemButton=array();
    $itemButton["text"]="Cancel";
    $itemButton["id"]="buttonCancel";
    $itemButton["handler"]= "copyDialogCancelButtonFunction";
    $functionsToReplace["copyDialogCancelButtonFunction"]="function() { Ext.getCmp('dialog').destroy(); }";
    $copyDialog["buttons"][]=$itemButton;

    $finalResponse=G::json_encode($copyDialog);
    foreach ($functionsToReplace as $key => $originalFunction) {
        $finalResponse=str_replace('"'.$key.'"',$originalFunction,$finalResponse);
    }
    $finalResponse=str_replace("URL_SCRIPT","../appFolder/appFolderAjax.php",$finalResponse);
    echo ($finalResponse);
}

function copyExecute()
{
    copyMoveExecute("copy");
}

function moveExecute()
{
    copyMoveExecute("move");
}

function copyMoveExecute($type)
{
    uploadExternalDocument();
}

function documentVersionHistory()
{
    $folderID = $_POST ['folderID'] != '0' ? $_POST ['folderID'] == 'NA' ? "" : $_POST ['folderID'] : $rootFolder;
    $folderContent = $oPMFolder->getFolderContent ($folderID, array ($_POST ['appDocId']));

    array_unshift ($folderContent, array ('id' => 'char'));
    if (! is_array ($folderContent)) {
        echo $folderContent;
        exit ();
    }

    $_DBArray ['PM_FOLDER_DOC_HISTORY'] = $folderContent;
    $_SESSION ['_DBArray'] = $_DBArray;

    G::LoadClass ('ArrayPeer');
    $c = new Criteria ('dbarray');
    $c->setDBArrayTable ('PM_FOLDER_DOC_HISTORY');
    $c->addAscendingOrderByColumn ('id');
    $G_PUBLISH = new Publisher ();
    require_once ('classes/class.xmlfield_InputPM.php');

    $G_PUBLISH->AddContent ('propeltable', 'paged-table', 'appFolder/appFolderDocumentListHistory', $c,
        array ('folderID' => $_POST ['folderID'] != '0' ? $_POST ['folderID'] == 'NA' ?
        "/" : $_POST ['folderID'] : $rootFolder));

    G::RenderPage ('publish', 'raw');
}

function overwriteFile ($node, $fileName) {
    global $RBAC;
    require_once ("classes/model/AppFolder.php");
    require_once ("classes/model/AppDocument.php");
    $appDocument = new AppDocument();
    $pMFolder = new AppFolder();
    $user = ($RBAC->userCanAccess('PM_ALLCASES') == 1) ? '' : $_SESSION['USER_LOGGED'];
    $folderContentObj = $pMFolder->getFolderContent ($node, array(), null, null, '', '', $user);
    foreach ($folderContentObj['documents'] as $key => $value) {
        if ($folderContentObj['documents'][$key]['APP_DOC_FILENAME'] == $fileName) {
            $appDocument->remove(trim($folderContentObj['documents'][$key]['APP_DOC_UID']), $folderContentObj['documents'][$key]['DOC_VERSION']);
        }
    }
}


function copyMoveExecuteTree($uidFolder, $newUidFolder)
{
    require_once ("classes/model/AppDocument.php");
    require_once ('classes/model/AppFolder.php');

    $appFoder = new AppFolder ();
    $folderContent = $appFoder->getFolderContent($uidFolder);
    $folderOrigin = $appFoder->getFolderStructure($uidFolder);
    $FolderParentUid = trim($newUidFolder);//$form['FOLDER_PARENT_UID'];
    $FolderName = $folderOrigin[$uidFolder]['NAME'];
    $newFolderContent = $appFoder->createFolder ($FolderName, $FolderParentUid, "new");

    $appDocument = new AppDocument();
    if ($_REQUEST['action'] == 'moveExecute') {
        $appFoder->remove($uidFolder,$folderOrigin[$uidFolder]['PARENT']);
    }
    $action = $_REQUEST['action'];
    foreach ($folderContent['documents'] as $keys => $value) {
        $docInfo = $appDocument->load($value['APP_DOC_UID'],$value['DOC_VERSION']);
        $docInfo['FOLDER_UID'] = $newFolderContent['folderUID'];
        $docInfo['APP_DOC_CREATE_DATE'] = date('Y-m-d H:i:s');
        $docInfo['APP_DOC_STATUS'] = 'ACTIVE';
        if ($action == 'copyExecute') {
            unset($docInfo['APP_DOC_UID']);
            $docUid = $appDocument->create($docInfo);
        } else {
            $appDocument->update($docInfo);
        }
    }
    return $newFolderContent['folderUID'];
}

function checkTree ($uidOriginFolder, $uidNewFolder)
{
    require_once ('classes/model/AppFolder.php');
    $appFoder = new AppFolder ();
    $newFoldercontent = copyMoveExecuteTree($uidOriginFolder, $uidNewFolder);
    $listfolder = $appFoder->getFolderList($uidOriginFolder);
    if (count($listfolder)>0) {
        foreach ($listfolder['folders'] as $key => $value) {
            copyMoveExecuteTree($value['FOLDER_UID'],$newFoldercontent);
        }
    } else {
        return;
    }
}

function uploadExternalDocument()
{
    $response['action']=$_POST['action']. " - ".$_POST['option'];
    $response['error']="error";
    $response['message']="error";
    $response['success']=false;
    $overwrite = (isset($_REQUEST['overwrite_files'])) ? $_REQUEST['overwrite_files'] : false;
    if (isset($_POST["confirm"]) && $_POST["confirm"] == "true") {
        //G::pr($_FILES);
        if (isset($_FILES['uploadedFile'])) {
            $uploadedInstances=count($_FILES['uploadedFile']['name']);
            $sw_error=false;
            $sw_error_exists=isset($_FILES['uploadedFile']['error']);
            $emptyInstances=0;
            $quequeUpload=array();
            //overwrite files
            if ($overwrite) {
                for ($i=0; $i<$uploadedInstances; $i++) {
                    overwriteFile($_REQUEST['dir'], stripslashes($_FILES['uploadedFile']['name'][$i]));
                }
            }
            // upload files & check for errors
            for ($i=0; $i<$uploadedInstances; $i++) {
                $errors[$i] = null;
                $tmp = $_FILES['uploadedFile']['tmp_name'][$i];
                $items[$i] = stripslashes($_FILES['uploadedFile']['name'][$i]);
                if ($sw_error_exists) {
                    $up_err = $_FILES['uploadedFile']['error'][$i];
                } else {
                    $up_err=(file_exists($tmp)?0:4);
                }
                if ($items[$i]=="" || $up_err==4) {
                    $emptyInstances++;
                    continue;
                }
                if ($up_err==1 || $up_err==2) {
                    $errors[$i]='miscfilesize';
                    $sw_error = true;
                    continue;
                }
                if ($up_err==3) {
                    $errors[$i]='miscfilepart';
                    $sw_error=true;
                    continue;
                }
                if (!@is_uploaded_file($tmp)) {
                    $errors[$i]='uploadfile';
                    $sw_error=true;
                    continue;
                }
                //The uplaoded files seems to be correct and ready to be uploaded. Add to the Queque
                $fileInfo=array("tempName"=>$tmp,"fileName"=>$items[$i]);
                $quequeUpload[]=$fileInfo;
            }
        } elseif (isset($_POST['selitems'])) {
            $response="";
            $response['msg']= "correct reload";
            $response['success']=true;
            if (isset($_REQUEST['option']) && isset($_REQUEST['copyMove'])) {
                if ($_REQUEST['option'] == 'directory' && $_REQUEST['copyMove'] == 'all') {
                    $response['action'] = $_POST['action']. " - ".$_POST['option'];
                    $response['error']  = "Complete";
                    $response['message']= str_replace("Execute", "", $_POST['action']). " ". "Complete";
                    $response['success']= 'success';
                    $response['node']   = '';
                    $_POST ['node']     = "";
                    $newFolderUid = checkTree($_REQUEST['dir'], $_REQUEST['new_dir']);
                }
                $_POST['selitems'] = array();
            } else {
                require_once ("classes/model/AppDocument.php");
                $oAppDocument = new AppDocument();
                foreach ($_POST['selitems'] as $docId) {
                    $arrayDocId = explode ('_',$docId);
                    $docInfo=$oAppDocument->load($arrayDocId[0]);
                    $docInfo['FOLDER_UID'] =  $_POST['new_dir'];
                    $docInfo['APP_DOC_CREATE_DATE'] = date('Y-m-d H:i:s');
                    $oAppDocument->update($docInfo);
                    //G::pr($docInfo);
                }
            }
        }
        //G::pr($quequeUpload);

        //Read. Instance Document classes
        if (!empty($quequeUpload)) {
            $docUid=$_POST['docUid'];
            $appDocUid=isset($_POST['APP_DOC_UID'])?$_POST['APP_DOC_UID']:"";
            $docVersion=isset($_POST['docVersion'])?$_POST['docVersion']:"";
            $actionType=isset($_POST['actionType'])?$_POST['actionType']:"";
            $folderId=$_POST['dir']==""?"/":$_POST['dir'];
            $appId=$_POST['appId'];
            $docType=isset($_POST['docType'])?$_GET['docType']:"INPUT";
            //save info

            require_once ("classes/model/AppDocument.php");
            require_once ('classes/model/AppFolder.php');
            require_once ('classes/model/InputDocument.php');

            $oInputDocument = new InputDocument();
            if ($docUid != -1) {
                $aID = $oInputDocument->load($docUid);
            } else {
                $oFolder=new AppFolder();
                $folderStructure=$oFolder->getFolderStructure($folderId);
                $aID=array('INP_DOC_DESTINATION_PATH'=>$folderStructure['PATH']);
            }

            $oAppDocument = new AppDocument();

            //Get the Custom Folder ID (create if necessary)
            $oFolder=new AppFolder();
            if ($docUid!=-1) {
                //krumo("jhl");
                $folderId=$oFolder->createFromPath($aID['INP_DOC_DESTINATION_PATH'],$appId);
                //Tags
                $fileTags=$oFolder->parseTags($aID['INP_DOC_TAGS'],$appId);
            } else {
                $folderId=$folderId;
                $fileTags="EXTERNAL";
            }
            foreach ($quequeUpload as $key => $fileObj) {
                switch ($actionType) {
                    case "R":
                        //replace
                        $aFields = array(
                            'APP_DOC_UID'           => $appDocUid,
                            'APP_UID'               => $appId,
                            'DOC_VERSION'           => $docVersion,
                            'DEL_INDEX'             => 1,
                            'USR_UID'               => $_SESSION['USER_LOGGED'],
                            'DOC_UID'               => $docUid,
                            'APP_DOC_TYPE'          => $docType,
                            'APP_DOC_CREATE_DATE'   => date('Y-m-d H:i:s'),
                            'APP_DOC_COMMENT'       => isset($_POST['form']['APP_DOC_COMMENT']) ?
                                $_POST['form']['APP_DOC_COMMENT'] : '',
                            'APP_DOC_TITLE'         => '',
                            'APP_DOC_FILENAME'      => $fileObj['fileName'],
                            'FOLDER_UID'            => $folderId,
                            'APP_DOC_TAGS'          => $fileTags
                        );
                        $oAppDocument->update($aFields);
                        break;
                    case "NV":
                        //New Version
                        $aFields = array(
                            'APP_DOC_UID'           => $appDocUid,
                            'APP_UID'               => $appId,
                            'DEL_INDEX'             => 1,
                            'USR_UID'               => $_SESSION['USER_LOGGED'],
                            'DOC_UID'               => $docUid,
                            'APP_DOC_TYPE'          => $docType,
                            'APP_DOC_CREATE_DATE'   => date('Y-m-d H:i:s'),
                            'APP_DOC_COMMENT'       => isset($_POST['form']['APP_DOC_COMMENT']) ?
                                $_POST['form']['APP_DOC_COMMENT'] : '',
                            'APP_DOC_TITLE'         => '',
                            'APP_DOC_FILENAME'      => $fileObj['fileName'],
                            'FOLDER_UID'            => $folderId,
                            'APP_DOC_TAGS'          => $fileTags
                        );
                        $oAppDocument->create($aFields);
                        break;
                    default:
                        //New
                        $aFields = array(
                            'APP_UID'               => $appId,
                            'DEL_INDEX'             => isset($_SESSION['INDEX'])?$_SESSION['INDEX']:1,
                            'USR_UID'               => $_SESSION['USER_LOGGED'],
                            'DOC_UID'               => $docUid,
                            'APP_DOC_TYPE'          => $docType,
                            'APP_DOC_CREATE_DATE'   => date('Y-m-d H:i:s'),
                            'APP_DOC_COMMENT'       => isset($_POST['form']['APP_DOC_COMMENT']) ?
                                $_POST['form']['APP_DOC_COMMENT'] : '',
                            'APP_DOC_TITLE'         => '',
                            'APP_DOC_FILENAME'      => $fileObj['fileName'],
                            'FOLDER_UID'            => $folderId,
                            'APP_DOC_TAGS'          => $fileTags
                        );
                        $oAppDocument->create($aFields);
                        break;
                }
                $sAppDocUid = $oAppDocument->getAppDocUid();
                $iDocVersion = $oAppDocument->getDocVersion();
                $info = pathinfo($oAppDocument->getAppDocFilename());
                $ext = (isset($info['extension']) ? $info['extension'] : '');

                //save the file
                //if (!empty($_FILES['form'])) {
                //if ($_FILES['form']['error']['APP_DOC_FILENAME'] == 0) {
                $sPathName = PATH_DOCUMENT . $appId . PATH_SEP;
                $sFileName = $sAppDocUid . "_".$iDocVersion. '.' . $ext;
                G::uploadFile($fileObj['tempName'], $sPathName, $sFileName);

                //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
                $oPluginRegistry =& PMPluginRegistry::getSingleton();
                if ($oPluginRegistry->existsTrigger (PM_UPLOAD_DOCUMENT) && class_exists ('uploadDocumentData')) {
                    $oData['APP_UID']   = $appId;
                    $documentData = new uploadDocumentData (
                        $appId,
                        $_SESSION['USER_LOGGED'],
                        $sPathName . $sFileName,
                        $fileObj['fileName'],
                        $sAppDocUid
                    );
                    //$oPluginRegistry->executeTriggers (PM_UPLOAD_DOCUMENT , $documentData);
                    //unlink ($sPathName . $sFileName);
                }
                //end plugin
                if ($sw_error) {
                    // there were errors
                    $err_msg="";
                    for ($i=0; $i<$uploadedInstances; $i++) {
                        if ($errors[$i]==null) {
                            continue;
                        }
                        $err_msg .= $items[$i]." : ".$errors[$i]."\n";
                    }
                    $response['error']=$err_msg;
                    $response['message']=$err_msg;
                    $response['success']=false;
                } elseif ($emptyInstances==$uploadedInstances) {
                    $response['error']= G::LoadTranslation('ID_UPLOAD_LEAST_FILE');
                    $response['message']= G::LoadTranslation('ID_UPLOAD_LEAST_FILE');
                    $response['success']=false;
                } else {
                    $response['error']= G::LoadTranslation('ID_UPLOAD_COMPLETE');
                    $response['message']="Upload complete";
                    $response['success']=true;
                }
            }
        }
    }
    print_r(G::json_encode($response));
    /*
     G::LoadClass ('case');
     $oCase = new Cases ();

     $G_PUBLISH = new Publisher ();
     $Fields ['DOC_UID'] = "-1";

     $Fields ['appId'] = "00000000000000000000000000000000";

     $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'cases/cases_AttachInputDocumentGeneral', '', $Fields,
     'appFolderSaveDocument?UID=-1&appId=' . $Fields ['appId'] . "&folderId=" . $_POST ['folderID']);
     G::RenderPage ('publish', 'raw');
     */
}

function newFolder()
{
    require_once ("classes/model/AppFolder.php");
    $oPMFolder = new AppFolder ();
    //G::pr($_POST);
    if ($_POST ['dir']=="") {
        $_POST ['dir']="/";
    }
    if ($_POST ['dir']=="root") {
        $_POST ['dir']="/";
    }
    $folderStructure = $oPMFolder->getFolderStructure ($_POST ['dir']);
    //G::pr($folderStructure);
    $folderPath = $folderStructure ['PATH'];
    $parentUid = $_POST ['dir'];
    $folderUid = G::GenerateUniqueID ();

    $formNewFolder=array();

    $formNewFolder["xtype"]="form";
    $formNewFolder["id"]= "simpleform";
    $formNewFolder["labelWidth"]=125;
    $formNewFolder["url"]="../appFolder/appFolderAjax.php";
    $formNewFolder["dialogtitle"]= G::LoadTranslation('ID_CREATE_FOLDER');
    $formNewFolder["frame"]= true;
    $formNewFolder["items"]= array();

    $field=array();
    $field["xtype"]= "label";
    $field["fieldLabel"]= "Path";
    $field["name"]= "form[FOLDER_PATH]";
    $field["id"]= "form[FOLDER_PATH]";
    $field["width"]=175;
    $field["allowBlank"]=false;
    $field["value"]=$folderPath;
    $field["text"]=$folderPath;
    $formNewFolder["items"][]= $field;

    $field=array();
    $field["xtype"]= "hidden";
    $field["fieldLabel"]= "Uid";
    $field["name"]= "form[FOLDER_UID]";
    $field["id"]= "form[FOLDER_UID]";
    $field["width"]=175;
    $field["allowBlank"]=false;
    $field["value"]=$folderUid;
    $formNewFolder["items"][]= $field;

    $field=array();
    $field["xtype"]= "hidden";
    $field["fieldLabel"]= "Parent";
    $field["name"]= "form[FOLDER_PARENT_UID]";
    $field["id"]= "form[FOLDER_PARENT_UID]";
    $field["width"]=175;
    $field["allowBlank"]=false;
    $field["value"]=$parentUid;
    $formNewFolder["items"][]= $field;

    $field=array();
    $field["xtype"]= "textfield";
    $field["fieldLabel"]= "Name";
    $field["name"]= "form[FOLDER_NAME]";
    $field["id"]= "form[FOLDER_NAME]";
    $field["width"]=175;
    $field["allowBlank"]=false;
    $formNewFolder["items"][]= $field;

    $formNewFolder["buttons"]= array();

    $button                     = array();
    $button["text"]             = "Create";
    $button["handler"]          = 'handlerCreate';
    $formNewFolder["buttons"][] = $button;

    $button=array();
    $button["text"]= "Cancel";
    $button["handler"]= 'handlerCancel';
    $formNewFolder["buttons"][]= $button;

    $handlerCreate='function() {
                statusBarMessage("Please wait...", true,true);
                Ext.getCmp("simpleform").getForm().submit({
                    //reset: true,
                    reset: false,
                    success: function(form, action) {
                        statusBarMessage(action.result.message, false, true);
                        try{
                            dirTree.getSelectionModel().getSelectedNode().reload();
                        } catch(e) {}
                        datastore.reload();
                        Ext.getCmp("dialog").destroy();
                    },
                    failure: function(form, action) {
                        if(!action.result) return;
                        Ext.Msg.alert("Error!", action.result.error);
                        statusBarMessage(action.result.error, false, false);
                    },
                    scope: Ext.getCmp("simpleform"),
                    // add some vars to the request, similar to hidden fields
                    params: {option: "new",
                            action: "appFolderSave",
                            dir: datastore.directory,
                            confirm: "true"}
                })
            }';

    $handlerCancel='function() { Ext.getCmp("dialog").destroy(); }';

    $response=G::json_encode($formNewFolder);
    //This will add the functions to the Json response without quotes!
    $response=str_replace('"handlerCreate"',$handlerCreate,$response);
    $response=str_replace('"handlerCancel"',$handlerCancel,$response);
    print_r($response);

    /*
     $oFolder = new AppFolder ();
     $folderStructure = $oPMFolder->getFolderStructure ($_POST ['folderID']);
     $Fields ['FOLDER_PATH'] = $folderStructure ['PATH'];
     $Fields ['FOLDER_PARENT_UID'] = $_POST ['folderID'];
     $Fields ['FOLDER_UID'] = G::GenerateUniqueID ();
     $G_PUBLISH = new Publisher ();

     $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'appFolder/appFolderEdit', '', $Fields, 'appFolderSave');
     G::RenderPage ('publish', 'raw');
     */
}

function appFolderSave()
{
    require_once ("classes/model/AppFolder.php");
    $oPMFolder = new AppFolder ();
    $form = $_POST['form'];
    $FolderUid = $form['FOLDER_UID'];
    $FolderParentUid = $form['FOLDER_PARENT_UID'];
    $FolderName = $form['FOLDER_NAME'];
    $FolderCreateDate = 'now';
    $FolderUpdateDate = 'now';
    $response['action']=$_POST['action']. " - ".$_POST['option'];
    $response['error']="error";
    $response['message']="error";
    $response['success']=false;
    $folderCreateResponse = $oPMFolder->createFolder ($FolderName, $FolderParentUid, "new");

    $response=array_merge($response,$folderCreateResponse);

    print_r(G::json_encode($response));
}

function documentInfo()
{
    $oFolder = new AppFolder ();
    $Fields = $oPMFolder->getCompleteDocumentInfo ($_POST ['appId'], $_POST ['appDocId'], $_POST ['docVersion'],
        $_POST ['docID'], $_POST ['usrUid']);
    $G_PUBLISH = new Publisher ();

    $G_PUBLISH->AddContent ('xmlform', 'xmlform', 'appFolder/appFolderDocumentInfo', '', $Fields, '');
    G::RenderPage ('publish', 'raw');
}

function documentdelete()
{
    include_once ("classes/model/AppDocument.php");
    $oAppDocument = new AppDocument ();
    $oAppDocument->remove($_POST['sFileUID'],$_POST['docVersion']);
    /*we need to delete fisicaly the file use the follow code
     $appId= "00000000000000000000000000000000";
     $sPathName = PATH_DOCUMENT . $appId . PATH_SEP;
     unlink($sPathName.$_POST['sFileUID'].'_1.jpg');*/
}

function deletePMFolder()
{
    include_once ("classes/model/AppFolder.php");
    $oAppFoder = new AppFolder ();
    $oAppFoder->remove($_POST['sFileUID'],$_POST['rootfolder']);

}

function getMime($fileName)
{
    $fileName=basename($fileName);
    $fileNameA=explode(".",$fileName);
    $return['description']=G::LoadTranslation("MIME_DES_FILE");
    $return['icon']="/images/documents/extension/document.png";
    if (count($fileNameA)>1) {
        $extension=$fileNameA[count($fileNameA)-1];
        if (file_exists(PATH_HTML."images/documents/extension/".strtolower($extension).".png")) {
            $return['description']=G::LoadTranslation("MIME_DES_".strtoupper($extension));
            $return['icon']="/images/documents/extension/".strtolower($extension).".png";
        }
    }
    return $return;
}

function get_max_file_size()
{
    // get php max_upload_file_size
    return calc_php_setting_bytes(ini_get("upload_max_filesize"));
}

function get_max_upload_limit()
{
    return calc_php_setting_bytes(ini_get('post_max_size'));
}

function calc_php_setting_bytes($value)
{
    //if(@eregi("G$",$value)) {
    //    $value = substr($value,0,-1);
    //    $value = round($value*1073741824);
    //} elseif (@eregi("M$",$value)) {
    //    $value = substr($value,0,-1);
    //    $value = round($value*1048576);
    //} elseif (@eregi("K$",$value)) {
    //    $value = substr($value,0,-1);
    //    $value = round($value*1024);
    //}
    if (@preg_match("/G$/i", $value)) {
        $value = substr($value, 0, -1);
        $value = round($value * 1073741824);
    } else {
        if (@preg_match("/M$/i", $value)) {
            $value = substr($value, 0, -1);
            $value = round($value * 1048576);
        } else {
            if (@preg_match("/K$/i", $value)) {
                $value = substr($value, 0, -1);
                $value = round($value * 1024);
            }
        }
    }
    return $value;
}

function get_abs_item($dir, $item)
{
    // get absolute file+path
    if (is_array($item)) {
        // FTP Mode
        $abs_item = '/' . get_abs_dir($dir)."/".$item['name'];
        if (get_is_dir($item)) {
            $abs_item.='/';
        }
        return extPathName($abs_item);
    }
    return extPathName(get_abs_dir($dir)."/".$item);
}

function extPathName($p_path, $p_addtrailingslash=false)
{
    $retval = "";

    $isWin = (substr(PHP_OS, 0, 3) == 'WIN');

    if ($isWin) {
        $retval = str_replace('/', '\\', $p_path);
        if ($p_addtrailingslash) {
            if (substr($retval, -1) != '\\') {
                $retval .= '\\';
            }
        }

        // Check if UNC path
        $unc = substr($retval,0,2) == '\\\\' ? 1 : 0;

        // Remove double \\
        $retval = str_replace('\\\\', '\\', $retval);

        // If UNC path, we have to add one \ in front or everything breaks!
        if ($unc == 1) {
            $retval = '\\'.$retval;
        }
    } else {
        $retval = str_replace('\\', '/', $p_path);
        if ($p_addtrailingslash) {
            if (substr($retval, -1) != '/') {
                $retval .= '/';
            }
        }

        // Check if UNC path
        $unc = substr($retval,0,2) == '//' ? 1 : 0;

        // Remove double //
        $retval = str_replace('//','/',$retval);

        // If UNC path, we have to add one / in front or everything breaks!
        if ($unc == 1) {
            $retval = '/'.$retval;
        }
    }
    return $retval;
}

