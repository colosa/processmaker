<?php
require_once ("classes/interfaces/dashletInterface.php");





class dashlet{className} implements DashletInterface
{
  const version = '1.0';

  private $role;
  private $note;

  public static function getAdditionalFields($className)
  {
    $additionalFields = array();

    ///////
    $cnn = Propel::getConnection("rbac");
    $stmt = $cnn->createStatement();

    $arrayRole = array();

    $sql = "SELECT ROL_CODE
            FROM   ROLES
            WHERE  ROL_SYSTEM = '00000000000000000000000000000002' AND ROL_STATUS = 1
            ORDER BY ROL_CODE ASC";
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();

      $arrayRole[] = array($row["ROL_CODE"], $row["ROL_CODE"]);
    }

    ///////
    $storeRole = new stdclass();
    $storeRole->xtype = "arraystore";
    $storeRole->idIndex = 0;
    $storeRole->fields = array("value", "text");
    $storeRole->data = $arrayRole;

    ///////
    $cboRole = new stdclass();
    $cboRole->xtype = "combo";
    $cboRole->name = "DAS_ROLE";

    $cboRole->valueField = "value";
    $cboRole->displayField = "text";
    $cboRole->value = $arrayRole[0][0];
    $cboRole->store = $storeRole;

    $cboRole->triggerAction = "all";
    $cboRole->mode = "local";
    $cboRole->editable = false;

    $cboRole->width = 320;
    $cboRole->fieldLabel = "Role";
    $additionalFields[] = $cboRole;

    ///////
    $txtNote = new stdclass();
    $txtNote->xtype = "textfield";
    $txtNote->name = "DAS_NOTE";
    $txtNote->fieldLabel = "Note";
    $txtNote->width = 320;
    $txtNote->value = null;
    $additionalFields[] = $txtNote;

    ///////
    return ($additionalFields);
  }

  public static function getXTemplate($className)
  {
    return "<iframe src=\"{" . "page" . "}?DAS_INS_UID={" . "id" . "}\" width=\"{" . "width" . "}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->role = $config["DAS_ROLE"];
    $this->note = $config["DAS_NOTE"];
  }

  public function render($width = 300)
  {
    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();

    $arrayUser = array();

    $sql = "SELECT USR.USR_USERNAME, USR.USR_FIRSTNAME, USR.USR_LASTNAME, USR.USR_STATUS
            FROM   USERS AS USR
            WHERE  USR.USR_ROLE = '" . $this->role . "'
            ORDER BY USR.USR_USERNAME ASC";
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next()) {
      $row = $rsSQL->getRow();

      $arrayUser[] = array("userName" => $row["USR_USERNAME"], "fullName" => $row["USR_FIRSTNAME"] . " " . $row["USR_LASTNAME"], "status" => $row["USR_STATUS"]);
    }

    ///////
    $dashletView = new dashlet{className}View($arrayUser, $this->note);
    $dashletView->templatePrint();
  }
}





class dashlet{className}View extends Smarty
{
  private $smarty;

  private $user;
  private $note;

  public function __construct($u, $n)
  {
    $this->user = $u;
    $this->note = $n;

    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . "smarty/configs";
    $this->smarty->caching      = false;
    $this->smarty->templateFile = PATH_PLUGINS . "{className}" . PATH_SEP . "views" . PATH_SEP . "dashlet{className}.html";
  }

  public function templateRender()
  {
    $this->smarty->assign("user", $this->user);
    $this->smarty->assign("note", $this->note);

    return ($this->smarty->fetch($this->smarty->templateFile));
  }

  public function templatePrint()
  {
    echo $this->templateRender();
    exit(0);
  }
}
?>