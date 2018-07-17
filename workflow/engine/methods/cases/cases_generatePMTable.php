<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$filter = new InputFilter();
$_POST = $filter->xssFilterHard($_POST);
require_once ("classes/model/AdditionalTables.php");
require_once ("classes/model/Fields.php");
// passing the parameters
$pmTableName = (isset( $_POST['tableName'] )) ? $_POST['tableName'] : 'contenders';
$pmTableFields = (isset( $_POST['tableFields'] )) ? G::json_decode( $_POST['tableFields'] ) : array ();

// default parameters
//$pmTableName   = 'Sender';
$pmTableFields = array (array ('FLD_NAME' => 'APP_UID'
),array ('FLD_NAME' => 'CON_NAME'
),array ('FLD_NAME' => 'CON_ADDR'
),array ('FLD_NAME' => '_cedula'
)
);

// setting the data to assemble the table
$aData = array ();
$aData['ADD_TAB_NAME'] = $pmTableName;
// creating the objects to create the table and records
$oFields = new Fields();
$oAdditionalTables = new AdditionalTables();

$sAddTabUid = $oAdditionalTables->create( $aData, $pmTableFields );

foreach ($pmTableFields as $iRow => $aRow) {
    $pmTableFields[$iRow]['FLD_NAME'] = strtoupper( $aRow['FLD_NAME'] );
    $pmTableFields[$iRow]['FLD_DESCRIPTION'] = isset( $aRow['FLD_DESCRIPTION'] ) ? $aRow['FLD_DESCRIPTION'] : $aRow['FLD_NAME'];
    $pmTableFields[$iRow]['FLD_TYPE'] = isset( $aRow['FLD_TYPE'] ) ? $aRow['FLD_TYPE'] : 'VARCHAR';
    $pmTableFields[$iRow]['FLD_SIZE'] = isset( $aRow['FLD_SIZE'] ) ? $aRow['FLD_SIZE'] : '32';
    $pmTableFields[$iRow]['FLD_NULL'] = isset( $aRow['FLD_NULL'] ) ? $aRow['FLD_NULL'] : 'off';
    $pmTableFields[$iRow]['FLD_AUTO_INCREMENT'] = isset( $aRow['FLD_AUTO_INCREMENT'] ) ? $aRow['FLD_AUTO_INCREMENT'] : 'off';
    $pmTableFields[$iRow]['FLD_KEY'] = isset( $aRow['FLD_KEY'] ) ? $aRow['FLD_KEY'] : 'off';
    $pmTableFields[$iRow]['FLD_FOREIGN_KEY'] = isset( $aRow['FLD_FOREIGN_KEY'] ) ? $aRow['FLD_FOREIGN_KEY'] : 'off';
    $pmTableFields[$iRow]['FLD_FOREIGN_KEY_TABLE'] = isset( $aRow['FLD_FOREIGN_KEY_TABLE'] ) ? $aRow['FLD_FOREIGN_KEY_TABLE'] : '';
}

foreach ($pmTableFields as $iRow => $aRow) {

    $oFields->create( array ('FLD_INDEX' => $iRow + 1,'ADD_TAB_UID' => $sAddTabUid,'FLD_NAME' => $aRow['FLD_NAME'],'FLD_DESCRIPTION' => isset( $aRow['FLD_DESCRIPTION'] ) ? $aRow['FLD_DESCRIPTION'] : '','FLD_TYPE' => isset( $aRow['FLD_TYPE'] ) ? $aRow['FLD_TYPE'] : 'VARCHAR','FLD_SIZE' => isset( $aRow['FLD_SIZE'] ) ? $aRow['FLD_SIZE'] : '32','FLD_NULL' => ($aRow['FLD_NULL'] == 'on' ? 1 : 0),'FLD_AUTO_INCREMENT' => ($aRow['FLD_AUTO_INCREMENT'] == 'on' ? 1 : 0),'FLD_KEY' => ($aRow['FLD_KEY'] == 'on' ? 1 : 0),'FLD_FOREIGN_KEY' => ($aRow['FLD_FOREIGN_KEY'] == 'on' ? 1 : 0),'FLD_FOREIGN_KEY_TABLE' => isset( $aRow['FLD_FOREIGN_KEY_TABLE'] ) ? $aRow['FLD_FOREIGN_KEY_TABLE'] : '') );

    $aFields[] = array ('sType' => isset( $aRow['FLD_TYPE'] ) ? $aRow['FLD_TYPE'] : 'VARCHAR','iSize' => isset( $aRow['FLD_SIZE'] ) ? $aRow['FLD_SIZE'] : '32','sFieldName' => $aRow['FLD_NAME'],'bNull' => ($aRow['FLD_NULL'] == 'on' ? 1 : 0),'bAI' => ($aRow['FLD_AUTO_INCREMENT'] == 'on' ? 1 : 0),'bPrimaryKey' => ($aRow['FLD_KEY'] == 'on' ? 1 : 0));
}

$oAdditionalTables->createTable( strtoupper( $pmTableName ), 'wf', $aFields );
$oAdditionalTables->createPropelClasses( strtoupper( $pmTableName ), $pmTableName, $pmTableFields, $sAddTabUid );

require_once ("classes/model/Application.php");
require_once ("classes/model/AdditionalTables.php");
require_once ("classes/model/Fields.php");

$Criteria = new Criteria( 'workflow' );
$Criteria->addSelectColumn( ApplicationPeer::APP_UID );
$Criteria->addSelectColumn( ApplicationPeer::APP_DATA );

//    $Criteria->add (AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');
$oDataset = ApplicationPeer::doSelectRS( $Criteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
$oDataset->next();
$aProcesses = array ();
$i = 0;
while ($aRow = $oDataset->getRow()) {
    $appuid = $aRow['APP_UID'];
    $data = unserialize( $aRow['APP_DATA'] );
    $cedula = '234' . rand( 1000, 999999 );
    $nombre = 'nombre ' . rand( 1000, 999999 );
    $direccion = 'direccion ' . rand( 1000, 999999 );
    if (isset( $data['_cedula'] )) {
        $cedula = $data['_cedula'];
        $nombre = isset( $data['_nombre'] ) ? $data['_nombre'] : '';
        $direccion = isset( $data['_direccion'] ) ? $data['_direccion'] : '';
        print "$i $appuid $cedula <br>";
    }
    //	  print_r ( $aRow);
    $sql = "insert CONTENDERS VALUES ( '$appuid', '$nombre', '$direccion', '$cedula' )";

    $con = Propel::getConnection( 'workflow' );
    $stmt = $con->createStatement();
    $rs = $stmt->executeQuery( $sql, ResultSet::FETCHMODE_ASSOC );

    $i ++;
    //    if ( $i == 100 ) die;
    /*	if ( strpos ( $aRow['APP_DATA'], 'cedula' ) !== false ) {
      print_r ( $aRow );
      print "<hr>";
    $i++;
    if ( $i == 10 ) die;
    }
    */
    $oDataset->next();
}
print "--$i--";

