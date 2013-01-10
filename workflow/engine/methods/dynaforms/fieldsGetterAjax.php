<?php
// added by gustavo cruz gustavo-at-colosa.com
/**
 * this function validates which fields cannot be part of a
 * grid dynaform those are: password, title, subtitle, button, submit,
 * reset, listbox, checkbox, check group, radio group, file, javascript
 * and obviously grid.
 *
 * @name apply_properties
 * @author gustavo cruz
 * @access public
 * @param $gridFields
 * @return $invalidFields
 */
G::LoadClass( 'xmlDb' );

function validateGridConversion ($gridFields)
{
    $invalidFields = array ();
    foreach ($gridFields as $value) {

        switch ($value['TYPE']) {
            case 'title':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'checkbox':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'radiogroup':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'submit':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'password':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'subtitle':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'button':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'reset':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'listbox':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'checkgroup':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'javascript':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
            case 'grid':
                $invalidFields[] = $value['XMLNODE_NAME'];
                break;
        }
    }
    return $invalidFields;
}
// end


// added by gustavo cruz gustavo-at-colosa.com
/**
 * this function get the fields that are part of the temporal
 * dynaform file.
 *
 * @name getTemporalFields
 * @author gustavo cruz
 * @access public
 * @param $file - the name of the dynaform file
 * @return invalidFields string
 */
function getTemporalFields ($file)
{
    try {
        //$G_PUBLISH->AddContent('pagedtable', 'paged-table', 'dynaforms/fields_List', 'display:none', $Parameters , '', SYS_URI.'dynaforms/dynaforms_PagedTableAjax');
        $i = 0;
        $aFields = array ();
        $aFields[] = array ('XMLNODE_NAME' => 'char','TYPE' => 'char','UP' => 'char','DOWN' => 'char');
        $oSession = new DBSession( new DBConnection( PATH_DYNAFORM . $file . '_tmp0.xml', '', '', '', 'myxml' ) );
        $oDataset = $oSession->Execute( 'SELECT * FROM dynaForm WHERE NOT( XMLNODE_NAME = "" ) AND TYPE <> "pmconnection"' );
        $iMaximun = $oDataset->count();
        while ($aRow = $oDataset->Read()) {
            $aFields[] = array ('XMLNODE_NAME' => $aRow['XMLNODE_NAME'],'TYPE' => $aRow['TYPE'],'UP' => ($i > 0 ? G::LoadTranslation( 'ID_UP' ) : ''),'DOWN' => ($i < $iMaximun - 1 ? G::LoadTranslation( 'ID_DOWN' ) : ''),'row__' => ($i + 1));
            $i ++;
        }
        // print_r($aFields);
        // die;
    } catch (Exception $e) {
    }
    $invalidFields = validateGridConversion( $aFields );
    if (count( $invalidFields ) > 0) {
        return (implode( ", ", $invalidFields ));
    } else {
        return "ok";
    }
}
// here make a response of the invalid fields for the Ajax request
echo getTemporalFields( $_POST['FILENAME'] );
// end

