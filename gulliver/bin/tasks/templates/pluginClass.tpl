<?php
/**
 * class.{className}.php
 *  
 */

  class {className}Class extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . '{className}' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }

    function getFieldsForPageSetup()
    {
    }

    function updateFieldsForPageSetup()
    {
    }

<!-- START BLOCK : report -->

    //here we are defining the available charts, the dashboard setup will call this function to know the charts
    function getAvailableReports() {
      return array (
        array('uid' => '{className}Report_1', 'title' => '{className} Test Report (users)'),
        //array('uid' => '{className}Report_2', 'title' => '{className} Test Report (groups)')
      );
    }    
    
    function getReport($reportName) {
      $obj = new StdClass();
      switch ($reportName) {
        case '{className}Report_1':
          $obj->title = '{className} Test Report (users)';
        break;
        case '{className}Report_2':
          $obj->title = '{className} Test Report (users)';
        break;
        default:
          $obj->title = 'default ....';
        break;
      }
      return $obj;
    }

    function {className}Report_1() {
      global $G_PUBLISH;

      $sDelimiter = DBAdapter::getStringDelimiter();
      $aUsers   = array();
      $aUsers[] = array('USR_UID'       => 'char',
                        'USR_NAME'      => 'char',
                        'USR_FIRSTNAME' => 'char',
                        'USR_LASTNAME'  => 'char',
                        'USR_EMAIL'     => 'char',
                        'USR_ROLE'      => 'char',);
                          
      $con = Propel::getConnection('workflow');
      $sql = 'SELECT USR_UID,USR_USERNAME,USR_FIRSTNAME,USR_LASTNAME,USR_EMAIL,USR_ROLE FROM USERS';
      $stmt = $con->createStatement();
      $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      while (is_array($row)) {
        $aUsers[] = array('USR_UID'       => $row['USR_UID'],
                          'USR_NAME'      => $row['USR_USERNAME'],
                          'USR_FIRSTNAME' => $row['USR_FIRSTNAME'],
                          'USR_LASTNAME'  => $row['USR_LASTNAME'],
                          'USR_EMAIL'     => $row['USR_EMAIL'],
                          'USR_ROLE'      => $row['USR_ROLE']);
        $rs->next();
        $row = $rs->getRow();
      }

      global $_DBArray;
      $_DBArray['users']  = $aUsers;
      $_SESSION['_DBArray'] = $_DBArray;
      
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('users');
      $oCriteria->addDescendingOrderByColumn('USR_USERNAME');
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('propeltable', 'paged-table', '{className}/report', $oCriteria);
      G::RenderPage('publish');
      return 1;
    }
<!-- END BLOCK : report -->
  }
?>