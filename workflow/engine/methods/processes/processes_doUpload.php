<?php
sleep( 1 );
global $RBAC;
if ( $RBAC->userCanAccess('PM_FACTORY') == 1) {

    if (isset( $_SESSION['processes_upload'] )) {
        $form = $_SESSION['processes_upload'];
        switch ($form['MAIN_DIRECTORY']) {
            case 'mailTemplates':
                $sDirectory = PATH_DATA_MAILTEMPLATES . $form['PRO_UID'] . PATH_SEP . ($form['CURRENT_DIRECTORY'] != '' ? $form['CURRENT_DIRECTORY'] . PATH_SEP : '');
                break;
            case 'public':
                $sDirectory = PATH_DATA_PUBLIC . $form['PRO_UID'] . PATH_SEP . ($form['CURRENT_DIRECTORY'] != '' ? $form['CURRENT_DIRECTORY'] . PATH_SEP : '');
                break;
            default:
                die();
                break;
        }
        G::LoadClass('processes');
        $app = new Processes();
        if (!$app->processExists($form['PRO_UID'])) {
            echo G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED');
            die;
        }
        
    }

    if ($_FILES['form']['error'] == "0") {
        G::uploadFile( $_FILES['form']['tmp_name'], $sDirectory, $_FILES['form']['name'] );
        $fp = fopen($sDirectory . $_FILES['form']['name'], 'rw');
        $content = fread($fp, filesize($sDirectory . $_FILES['form']['name']));
        $fields = array('!--', '--', '!DOCTYPE', 'a', 'abbr', 'acronym', 'address', 'applet', 'area',
            'article', 'aside', 'audio', 'b', 'base', 'basefont', 'bdi', 'bdo', 'big',
            'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite',
            'code', 'col', 'colgroup', 'command', 'datalist', 'dd', 'del', 'details', 'dfn',
            'dialog', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'figcaption',
            'figure', 'font', 'footer', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4',
            'h5', 'h6', 'head', 'header', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins',
            'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'map', 'mark', 'menu', 'meta',
            'meter', 'nav', 'noframes', 'noscript', 'object', 'ol', 'optgroup', 'option',
            'output', 'p', 'param', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp',
            'script', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong', 'style',
            'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead',
            'time', 'title', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr');
        $content = G::sanitizeInput($content, $fields, array(), 0, 1, 0);
        fwrite( $fp, $content );
        fclose($fp);
        $msg = "Uploaded (" . (round( (filesize( $sDirectory . $_FILES['form']['name'] ) / 1024) * 10 ) / 10) . " kb)";
        $result = 1;
        //echo $sDirectory.$_FILES['form']['name'];
    } else {
        $msg = "Failed";
        $result = 0;
    }
    
    echo "{'result': $result, 'msg':'$msg'}";
}