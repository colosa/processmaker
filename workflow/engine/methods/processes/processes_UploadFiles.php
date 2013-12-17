<?php
if ( $RBAC->userCanAccess('PM_FACTORY') == 1) {
    G::LoadClass('processes');
    $app = new Processes();
    if (!$app->processExists($_REQUEST['pro_uid'])) {
        echo G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED');
        die;
    }
    switch ($_POST['form']['MAIN_DIRECTORY']) {
        case 'mailTemplates':
            $sDirectory = PATH_DATA_MAILTEMPLATES . $_POST['form']['PRO_UID'] . PATH_SEP . ($_POST['form']['CURRENT_DIRECTORY'] != '' ? $_POST['form']['CURRENT_DIRECTORY'] . PATH_SEP : '');
            break;
        case 'public':
            $sDirectory = PATH_DATA_PUBLIC . $_POST['form']['PRO_UID'] . PATH_SEP . ($_POST['form']['CURRENT_DIRECTORY'] != '' ? $_POST['form']['CURRENT_DIRECTORY'] . PATH_SEP : '');
            break;
        default:
            die();
            break;
    }
    for ($i = 1; $i <= 5; $i ++) {
        if ($_FILES['form']['tmp_name']['FILENAME' . (string) $i] != '') {
            G::uploadFile( $_FILES['form']['tmp_name']['FILENAME' . (string) $i], $sDirectory, $_FILES['form']['name']['FILENAME' . (string) $i] );
            $fp = fopen($sDirectory, $_FILES['form']['name']['FILENAME' . (string) $i] , 'rw');
            $content = fread($fp, filesize($sDirectory, $_FILES['form']['name']['FILENAME' . (string) $i] ));
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
        }
    }
}
die( '<script type="text/javascript">parent.goToDirectoryforie(\'' . $_POST['form']['PRO_UID'] . '\', \'' . $_POST['form']['MAIN_DIRECTORY'] . '\', \'' . $_POST['form']['CURRENT_DIRECTORY'] . '\');</script>' );
