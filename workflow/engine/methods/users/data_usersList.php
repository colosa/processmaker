<?php
/**
 * data_usersList.php
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

isset( $_POST['textFilter'] ) ? $filter = $_POST['textFilter'] : $filter = '';

$sDelimiter = DBAdapter::getStringDelimiter();

$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( UsersPeer::USR_UID );

$sDataBase = 'database_' . strtolower( DB_ADAPTER );
if (G::LoadSystemExist( $sDataBase )) {
    $oDataBase = new database();
    $oCriteria->addAsColumn( 'USR_COMPLETENAME', $oDataBase->concatString( "USR_LASTNAME", "' '", "USR_FIRSTNAME" ) );
    //$oCriteria->addAsColumn('USR_PHOTO', $oDataBase->concatString("'".PATH_IMAGES_ENVIRONMENT_USERS."'", "USR_UID","'.gif'"));
}

$oCriteria->addSelectColumn( UsersPeer::USR_USERNAME );
$oCriteria->addSelectColumn( UsersPeer::USR_EMAIL );
$oCriteria->addSelectColumn( UsersPeer::USR_ROLE );
$oCriteria->addSelectColumn( UsersPeer::USR_DUE_DATE );
//$oCriteria->addAsColumn('USR_VIEW', $sDelimiter . G::LoadTranslation('ID_DETAIL') . $sDelimiter);
//$oCriteria->addAsColumn('USR_EDIT', $sDelimiter . G::LoadTranslation('ID_EDIT') . $sDelimiter);
//$oCriteria->addAsColumn('USR_DELETE', $sDelimiter . G::LoadTranslation('ID_DELETE') . $sDelimiter);
//$oCriteria->addAsColumn('USR_AUTH', $sDelimiter . G::LoadTranslation('ID_AUTHENTICATION') . $sDelimiter);
//$oCriteria->addAsColumn('USR_REASSIGN', $sDelimiter . G::LoadTranslation('ID_REASSIGN_CASES') . $sDelimiter);
$oCriteria->add( UsersPeer::USR_STATUS, array ('CLOSED'
), Criteria::NOT_IN );

if ($filter != '') {
    $cc = $oCriteria->getNewCriterion( UsersPeer::USR_USERNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_FIRSTNAME, '%' . $filter . '%', Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( UsersPeer::USR_LASTNAME, '%' . $filter . '%', Criteria::LIKE ) ) );
    $oCriteria->add( $cc );
    //echo $oCriteria->toString();
}

$rs = UsersPeer::DoSelectRS( $oCriteria, Propel::getDbConnection('workflow_ro') );
$rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

$rows = Array ();
while ($rs->next()) {
    $rows[] = $rs->getRow();
    //	if (!file_exists($aux['USR_PHOTO'])) $aux['USR_PHOTO'] = 'public_html/images/user.gif';
    //	$rows[] = $aux;
}
echo '{users: ' . G::json_encode( $rows ) . '}';

