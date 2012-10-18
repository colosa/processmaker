<?php
/**
 * processes_List.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

$actionAjax = isset( $_REQUEST['actionAjax'] ) ? $_REQUEST['actionAjax'] : null;

if ($actionAjax == 'historyGridList_JXP') {

    G::LoadClass( 'case' );
    G::LoadClass( "BasePeer" );

    global $G_PUBLISH;
    $c = Cases::getTransferHistoryCriteria( $_SESSION['APPLICATION'] );

    $result = new stdClass();
    $aProcesses = Array ();

    $rs = GulliverBasePeer::doSelectRs( $c );
    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();
    for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
        $result = $rs->getRow();
        $result["ID_HISTORY"] = $result["PRO_UID"] . '_' . $result["APP_UID"] . '_' . $result["TAS_UID"];
        $aProcesses[] = $result;
        $rs->next();
    }

    $newDir = '/tmp/test/directory';
    $r = G::verifyPath( $newDir );
    $r->data = $aProcesses;
    $r->totalCount = 2;

    echo G::json_encode( $r );
}

if ($actionAjax == 'historyGridListChangeLogPanelBody_JXP') {
    //!dataInput
    $idHistory = $_REQUEST["idHistory"];
    //!dataInput


    //!dataSytem
    $idHistoryArray = explode( "*", $idHistory );
    $_REQUEST["PRO_UID"] = $idHistoryArray[0];
    $_REQUEST["APP_UID"] = $idHistoryArray[1];
    $_REQUEST["TAS_UID"] = $idHistoryArray[2];
    $_REQUEST["DYN_UID"] = "";

    ?>

<table bgcolor="white" height=100% width=100%>
	<tr>
		<td height=99%>
			<div
				style="width: 100%; overflow-y: scroll; overflow-x: hidden; max-height: 310px; _height: 310px; height: 310px; visibility: inherit;">

<?php
    require_once 'classes/model/AppHistory.php';
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'view', 'cases/cases_DynaformHistory' );
    G::RenderPage( 'publish', 'raw' );
    ?>


      </div>





		</td>
	</tr>
	<tr>
		<td height=30 valign=top>

			<table align=center cellspacing="0" class="x-btn x-btn-noicon"
				id="ext-comp-1043" style="width: 75px; margin-top: 0px;">
				<tbody class="x-btn-small x-btn-icon-small-left">
					<tr>
						<td class="x-btn-tl"><i>&nbsp;</i></td>
						<td class="x-btn-tc"></td>
						<td class="x-btn-tr"><i>&nbsp;</i></td>
					</tr>
					<tr>
						<td class="x-btn-ml"><i>&nbsp;</i></td>
						<td class="x-btn-mc"><em unselectable="on" class="">
								<button type="button" id="ext-gen105" class=" x-btn-text">OK</button>
						</em></td>
						<td class="x-btn-mr"><i>&nbsp;</i></td>
					</tr>
					<tr>
						<td class="x-btn-bl"><i>&nbsp;</i></td>
						<td class="x-btn-bc"></td>
						<td class="x-btn-br"><i>&nbsp;</i></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php

}

if ($actionAjax == "showDynaformHistoryGetNomDynaform_JXP") {
    require_once 'classes/model/ContentPeer.php';

    //!dataInput
    $idDin = $_REQUEST['idDin'];
    $dynDate = $_REQUEST["dynDate"];

    //!dataOuput
    $md5Hash = "";
    $dynTitle = '';

    $c = new Criteria();
    $c0 = $c->getNewCriterion( ContentPeer::CON_ID, $idDin );
    $c1 = $c->getNewCriterion( ContentPeer::CON_CATEGORY, 'DYN_TITLE' );
    $c0->addAnd( $c1 );
    $c->add( $c0 );
    $contentObjeto = ContentPeer::doSelectOne( $c );

    if (is_object( $contentObjeto )) {
        $dynTitle = $contentObjeto->getConValue();
    }

    $md5Hash = md5( $idDin . $dynDate );

    //assign task
    $result = new stdClass();
    $result->dynTitle = $dynTitle;
    $result->md5Hash = $md5Hash;

    echo G::json_encode( $result );

}

