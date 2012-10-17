<?php
/**
 * translationsAjax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
ini_set( 'display_errors', 'off' );
G::LoadInclude( 'ajax' );
$function = get_ajax_value( 'function' );
$cat = get_ajax_value( 'cat' );
$node = get_ajax_value( 'node' );
$lang = get_ajax_value( 'lang' );
$langLabel = get_ajax_value( 'langLabel' );
$text = get_ajax_value( 'text' );
$table = "TRANSLATION";
$dbc = new DBConnection();
$ses = new DBSession( $dbc );
switch ($function) {
    case "editLabel":
    case "changeLabel":
        $query = $ses->execute( "select * from $table where TRN_CATEGORY='$cat' and TRN_ID='$node' and TRN_LANG='$lang'", false );
        if ($query->count() === 0) {
            echo ("Not found $cat:$node:$lang in table '$table'");
            return;
        }
        if ($query->count() > 1) {
            echo ("The $cat:$node:$lang in table '$table' is not unique");
            return;
        }
        $res = $query->read();
        switch ($function) {
            case "editLabel":
                $myID = "input_" . $cat . "_" . $node . "_" . $lang;
                $myID = 'aux';
                echo ("<input id='$myID' type='text' value='" . htmlspecialchars( $res['TRN_VALUE'] ) . "' " . ajax_event( "onblur", "translationsAjax.php", 'lang_' . $cat . '_' . $node . '_' . $lang, "'function=changeLabel&cat=" . urlencode( $cat ) . "&node=" . urlencode( $node ) . "&lang=" . urlencode( $lang ) . "&langLabel='+encodeURI(getElementById('$myID').value)", '' ) . "/>");
                break;
            case "changeLabel":
                $update = $ses->execute( "update $table set TRN_VALUE='$langLabel' where TRN_CATEGORY='$cat' and TRN_ID='$node' and TRN_LANG='$lang'", false );
                $query = $ses->execute( "select * from $table where TRN_CATEGORY='$cat' and TRN_ID='$node' and TRN_LANG='$lang'", false );
                if ($query->count() === 0) {
                    echo ("Not found $cat:$node:$lang in table '$table'");
                    return;
                }
                if ($query->count() > 1) {
                    echo ("The $cat:$node:$lang in table '$table' is not unique");
                    return;
                }
                $res = $query->read();
                echo (htmlspecialchars( $res['TRN_VALUE'] ));
                break;
                break;
        }
        G::LoadClass( "translation" );

        $dbc = new DBConnection();
        $obj = new Translation();
        $obj->SetTo( $dbc );
        $translation2 = $obj->generateFileTranslation();
        break;
    case "listLanguage":
        $query = $ses->execute( "select distinct TRN_LANG from $table", false );
        $template = new TemplatePower( PATH_CORE . 'templates/tools/translationsTP.html' );
        $template->prepare();
        $template->newBlock( "languageList" );
        $template->assign( "ajaxDelLang", ajax_event( "onclick", "translationsAjax", "showSpace", "'function=delLanguage&lang='+encodeURI(getElementById('language').value)", 'hideLangBar' ) );

        for ($r = 1; $r <= $query->count(); $r ++) {
            $res = $query->read();
            $template->newBlock( "languageItem" );
            $template->assign( "langIdRadio", $res['TRN_LANG'] );
        }
        $template->printToScreen();
        break;
    case "show":
    case "search":
    case "addField":
    case "addLanguage":
    case "delLanguage":
    case "delField":
        switch ($function) {
            case "show":
                $query = $ses->execute( "select * from $table ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
            case "search":
                $query = $ses->execute( "select * from $table where (TRN_CATEGORY like '%$text%') or (TRN_ID like '%$text%') or (TRN_LANG like '%$text%') or (TRN_VALUE like '%$text%') ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
            case "addField":
                if (! defined( 'LANGUAGES' ))
                    define( 'LANGUAGES', SYS_LANG );
                $languages = explode( ",", LANGUAGES );
                foreach ($languages as $lang) {
                    //$langLabel=$cat;
                    /*					$update=$ses->execute("insert into $table(TRN_CATEGORY , TRN_ID , TRN_LANG , TRN_VALUE  )
															 values ('$cat','$node','$lang','$langLabel')",false);*/
                    $update = $ses->execute( "insert into $table(TRN_CATEGORY , TRN_ID , TRN_LANG , TRN_VALUE  )
															 values ('LABEL','$node','$lang','$langLabel')", false );
                }
                $query = $ses->execute( "select * from $table ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
            case "addLanguage":
                //SELECT distinct TRN_CATEGORY, TRN_ID, 'ne', 'new value' FROM TRANSLATION WHERE TRN_LANG<>'ne'
                //INSERT INTO TRANSLATION(TRN_CATEGORY, TRN_ID, TRN_LANG , TRN_VALUE ) SELECT distinct TRN_CATEGORY, TRN_ID, 'ne', TRN_VALUE FROM TRANSLATION WHERE TRN_LANG<>'ne'
                $query1 = $ses->execute( "select * from $table where TRN_LANG='$lang'", false );
                $query2 = $ses->execute( "select distinct TRN_CATEGORY, TRN_ID from $table", false );
                $existe = array ();
                for ($r = 1; $r <= $query1->count(); $r ++) {
                    $res = $query1->read();
                    $existe[$res['TRN_CATEGORY'] . "_" . $res['TRN_ID']] = $res;
                }
                for ($r = 1; $r <= $query2->count(); $r ++) {
                    $res = $query2->read();
                    $cat = $res['TRN_CATEGORY'];
                    $node = $res['TRN_ID'];
                    $langLabel = $res['TRN_CATEGORY'];
                    if (! array_key_exists( $cat . "_" . $node, $existe ))
/*						$update=$ses->execute("insert into $table(TRN_CATEGORY , TRN_ID , TRN_LANG , TRN_VALUE  )
															 values ('$cat','$node','$lang','$langLabel')",false);*/
                    $update = $ses->execute( "insert into $table(TRN_CATEGORY , TRN_ID , TRN_LANG , TRN_VALUE  )
															 values ('LABELS','$node','en','$langLabel')", false );
                    unset( $update );
                }
                unset( $existe );
                unset( $query1 );
                unset( $query2 );
                $query = $ses->execute( "select * from $table ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
            case "delLanguage":
                $update = $ses->execute( "delete from $table where TRN_LANG='$lang'", false );
                $query = $ses->execute( "select * from $table ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
            case "delField":
                $update = $ses->execute( "delete from $table where TRN_CATEGORY='$cat' and TRN_ID='$node' and TRN_LANG='$lang'", false );
                //echo("delete from $table where TRN_CATEGORY='$cat' and TRN_ID='$node' and TRN_LANG='$lang'");
                $query = $ses->execute( "select * from $table ORDER BY TRN_CATEGORY ASC , TRN_ID ASC , TRN_LANG ASC ", false );
                break;
        }
        $template = new TemplatePower( PATH_CORE . 'templates/tools/translationsTP.html' );
        $template->prepare();
        $template->newBlock( "CONTENT" );
        $template->newBlock( "table" );
        $aCat = "";
        $aNode = "";
        for ($i = 1; $i <= $query->count(); $i ++) {
            $template->newBlock( "row" );
            $res = $query->read();
            $cat = $res['TRN_CATEGORY'];
            $node = $res['TRN_ID'];
            $lang = $res['TRN_LANG'];
            $langLabel = $res['TRN_VALUE'];
            if ($cat != $aCat) {
                $template->newBlock( "TDcat" );
                $template->assign( "catId", $res['TRN_CATEGORY'] );
                $template->assign( "ajaxDelField", ajax_event( 'onclick', 'translationsAjax', 'showSpace', "'function=delField" . "&cat=" . urlencode( $cat ) . "&node=" . urlencode( $node ) . "&lang=" . urlencode( $lang ) . "'", '' ) );
            }
            if (($cat != $aCat) || ($node != $aNode)) {
                $template->newBlock( "TDnode" );
                $template->assign( "nodeId", $res['TRN_ID'] );
            }
            //$aCat=$cat;
            //$aNode=$node;
            $template->goToBlock( "row" );
            $template->assign( "catId", $res['TRN_CATEGORY'] );
            $template->assign( "nodeId", $res['TRN_ID'] );
            $template->assign( "langId", $lang );
            $template->assign( "langLabel", $langLabel );
            $template->assign( "ajaxLabel", "onclick=\"if (!document.getElementById('aux'))" . ajax_init( 'translationsAjax.php', 'lang_' . $cat . '_' . $node . '_' . $lang, "'function=editLabel" . "&cat=" . urlencode( $cat ) . "&node=" . urlencode( $node ) . "&lang=" . urlencode( $lang ) . "'", 'focusInputLabel' ) . '"' );
        }
        $template->printToScreen();
        break;
}
?>
<?php


function ajax_event ($event = "onclick", $page, $div, $param, $freturn = "")
{
    if ($freturn === '')
        $freturn = "''";
    return "$event=\"ajax_init('$page','$div',$param,$freturn)\"";
}

function ajax_init ($page, $div, $param, $freturn = "")
{
    if ($freturn == '')
        $freturn = "''";
    return "ajax_init('$page','$div',$param,$freturn);";
}

