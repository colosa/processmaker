<?php
/**
 * welcome.php
 *
 *
 */

try {

$rows[] = array ( 'uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );
$rows[] = array ( 'uid' => 11, 'name' => 'john',   'age' => 44, 'balance' => 123423 );
$rows[] = array ( 'uid' => 22, 'name' => 'Amy',    'age' => 33, 'balance' => 23456 );
$rows[] = array ( 'uid' => 33, 'name' => 'Dan',    'age' => 22, 'balance' => 34567 );
$rows[] = array ( 'uid' => 33, 'name' => 'Mike',   'age' => 21, 'balance' => 4567 );
$rows[] = array ( 'uid' => 44, 'name' => 'Paul',   'age' => 22, 'balance' => 567 );
$rows[] = array ( 'uid' => 55, 'name' => 'Wil',   'age' => 23, 'balance' => 67 );
$rows[] = array ( 'uid' => 66, 'name' => 'Ernest', 'age' => 24, 'balance' => 7 );
$rows[] = array ( 'uid' => 77, 'name' => 'Albert', 'age' => 25, 'balance' => 84567 );
$rows[] = array ( 'uid' => 88, 'name' => 'Sue',    'age' => 26, 'balance' => 94567 );
$rows[] = array ( 'uid' => 99, 'name' => 'Freddy', 'age' => 22, 'balance' => 04567 );

$_DBArray['user'] = $rows;
$_SESSION['_DBArray'] = $_DBArray;
//krumo ( $_DBArray );

    $c = new Criteria ('dbarray');
    $c->setDBArrayTable('user');
    //$c->add ( 'user.age', 122 , Criteria::GREATER_EQUAL );
    //$c->add ( 'user.balance', 3456 , Criteria::GREATER_EQUAL );
    $c->addAscendingOrderByColumn ('name');
/*
      $rs = ArrayBasePeer::doSelectRs ( $c );
      $rs->next();
      $row = $rs->getRow();
      while ( is_array ( $row ) ) {
        $rs->next();
        $row = $rs->getRow();
      }
*/
  /* Render page */
  $G_MAIN_MENU = '{projectName}';
  $G_ID_MENU_SELECTED     = 'WELCOME';
  $G_SUB_MENU = 'welcome';
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'login/welcome', $c );
  G::RenderPage( "publish" );

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish');
}
