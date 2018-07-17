<?php

$G_MAIN_MENU            = '{projectName}';
$G_SUB_MENU             = 'users';
$G_ID_MENU_SELECTED     = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'USERS';


  $con = Propel::getConnection('rbac');
  $sql = "SELECT USR_UID, USR_USERNAME, USR_FIRSTNAME, USR_LASTNAME, USR_EMAIL, USR_STATUS FROM RBAC_USERS";
  $stmt = $con->createStatement();
  $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
  $rs->next();
  $row = $rs->getRow();
  $rows[] = array ( 'uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );
  while ( is_array ( $row ) ) {
      if ( is_array( $row) ) $rows[] = $row;
      $rs->next();
      $row = $rs->getRow();
  }

  $_DBArray['user'] = $rows;
  $_SESSION['_DBArray'] = $_DBArray;

    $c = new Criteria ('dbarray');
    $c->setDBArrayTable('user');

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'users/usersList', $c );
  G::RenderPage('publish');
