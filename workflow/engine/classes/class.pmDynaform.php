<?php

/**
 * class.pmDynaform.php
 * Implementing pmDynaform library in the running case.
 *
 * @author Roly Rudy Gutierrez Pinto
 * @package engine.classes
 */
class pmDynaform
{

    public static $instance = null;
    public $fields = null;
    public $record = null;
    public $records = null;
    public $credentials = null;
    public $lang = SYS_LANG;
    public $langs = null;
    public $onPropertyRead = "onPropertyReadFormInstance";

    public function __construct($fields = array())
    {
        $this->fields = $fields;
        $this->getDynaform();
        $this->getDynaforms();
        $this->getCredentials();
        if (!isset($this->fields["APP_UID"])) {
            $this->fields["APP_UID"] = null;
        }
        if (isset($this->fields["APP_DATA"]["DYN_CONTENT_HISTORY"])) {
            $this->record["DYN_CONTENT"] = $this->fields["APP_DATA"]["DYN_CONTENT_HISTORY"];
        }
    }

    public function getDynaformTitle($idDynaform)
    {
        $d = new Dynaform();
        $d->setDynUid($idDynaform);
        $titleDynaform = $d->getDynTitle();
        return $titleDynaform;
    }

    public function getDynaform()
    {
        if (!isset($this->fields["CURRENT_DYNAFORM"])) {
            return;
        }
        if ($this->record != null) {
            return $this->record;
        }
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_VERSION);
        $a->addSelectColumn(DynaformPeer::DYN_LABEL);
        $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $a->addSelectColumn(DynaformPeer::PRO_UID);
        $a->addSelectColumn(DynaformPeer::DYN_UID);
        $a->add(DynaformPeer::DYN_UID, $this->fields["CURRENT_DYNAFORM"], Criteria::EQUAL);
        $ds = DynaformPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $ds->next();
        $row = $ds->getRow();
        $this->record = isset($row) ? $row : null;
        $this->langs = ($this->record["DYN_LABEL"] !== "" && $this->record["DYN_LABEL"] !== null) ? G::json_decode($this->record["DYN_LABEL"]) : null;
        return $this->record;
    }

    public function getDynaforms()
    {
        if ($this->record === null) {
            return;
        }
        if ($this->records != null) {
            return $this->records;
        }
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_UPDATE_DATE);
        $a->addSelectColumn(DynaformPeer::DYN_VERSION);
        $a->addSelectColumn(DynaformPeer::DYN_LABEL);
        $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $a->addSelectColumn(DynaformPeer::PRO_UID);
        $a->addSelectColumn(DynaformPeer::DYN_UID);
        $a->add(DynaformPeer::PRO_UID, $this->record["PRO_UID"], Criteria::EQUAL);
        $a->add(DynaformPeer::DYN_UID, $this->record["DYN_UID"], Criteria::NOT_EQUAL);
        $ds = DynaformPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $this->records = array();
        while ($ds->next()) {
            array_push($this->records, $ds->getRow());
        }
        return $this->records;
    }

    public function getCredentials()
    {
        $flagTrackerUser = false;

        if (!isset($_SESSION['USER_LOGGED'])) {
            if (!preg_match("/^.*\/" . SYS_SKIN . "\/tracker\/.*$/", $_SERVER["REQUEST_URI"])) {
                return;
            }

            $_SESSION["USER_LOGGED"] = "00000000000000000000000000000001";
            $flagTrackerUser = true;
        }
        if ($this->credentials != null) {
            return $this->credentials;
        }
        $a = $this->clientToken();
        $this->credentials = array(
            "accessToken" => $a["access_token"],
            "expiresIn" => $a["expires_in"],
            "tokenType" => $a["token_type"],
            "scope" => $a["scope"],
            "refreshToken" => $a["refresh_token"],
            "clientId" => $a["client_id"],
            "clientSecret" => $a["client_secret"]
        );

        if ($flagTrackerUser) {
            unset($_SESSION["USER_LOGGED"]);
        }

        return $this->credentials;
    }

    public function jsonr(&$json)
    {
        foreach ($json as $key => &$value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $this->jsonr($value);
            }
            if (!$sw1 && !$sw2) {
                //read event
                $fn = $this->onPropertyRead;
                if (function_exists($fn)) {
                    $fn($json, $key, $value);
                }
                //set properties from trigger
                $prefixs = array("@@", "@#", "@%", "@?", "@$", "@=");
                if (is_string($value) && in_array(substr($value, 0, 2), $prefixs)) {
                    $triggerValue = substr($value, 2);
                    if (isset($this->fields["APP_DATA"][$triggerValue])) {
                        $json->{$key} = $this->fields["APP_DATA"][$triggerValue];
                    }
                }
                //set properties from 'formInstance' variable
                if (isset($this->fields["APP_DATA"]["formInstance"])) {
                    $formInstance = $this->fields["APP_DATA"]["formInstance"];
                    if (!is_array($formInstance)) {
                        $formInstance = array($formInstance);
                    }
                    $nfi = count($formInstance);
                    for ($ifi = 0; $ifi < $nfi; $ifi++) {
                        $fi = $formInstance[$ifi];
                        if (is_object($fi) && isset($fi->id) && $key === "id" && $json->{$key} === $fi->id) {
                            foreach ($fi as $keyfi => $valuefi) {
                                if (isset($json->{$keyfi})) {
                                    $json->{$keyfi} = $valuefi;
                                }
                            }
                        }
                    }
                }
                //options & query
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "hidden" || $value === "dropdown" || $value === "checkgroup" || $value === "radio" || $value === "suggest" )) {
                    if (!isset($json->dbConnection))
                        $json->dbConnection = "none";
                    if (!isset($json->sql))
                        $json->sql = "";
                    $json->optionsSql = array();
                    if ($json->dbConnection !== "" && $json->dbConnection !== "none" && $json->sql !== "") {
                        $cnn = Propel::getConnection($json->dbConnection);
                        $stmt = $cnn->createStatement();
                        try {
                            $rs = $stmt->executeQuery(G::replaceDataField($json->sql, array()), \ResultSet::FETCHMODE_NUM);
                            while ($rs->next()) {
                                $row = $rs->getRow();
                                $option = new stdClass();
                                $option->value = $row[0];
                                $option->label = isset($row[1]) ? $row[1] : $row[0];
                                array_push($json->optionsSql, $option);
                            }
                        } catch (Exception $e) {
                            
                        }
                    }
                }
                //data
                if ($key === "type" && ($value === "text" || $value === "textarea" || $value === "hidden")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if (isset($json->optionsSql[0])) {
                        $json->data->value = $json->optionsSql[0]->value;
                        $json->data->label = $json->optionsSql[0]->value;
                    }
                    if ($json->defaultValue !== "") {
                        $json->data->value = $json->defaultValue;
                        $json->data->label = $json->defaultValue;
                    }
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name];
                    }
                }
                if ($key === "type" && ($value === "dropdown")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if ($json->defaultValue !== "") {
                        foreach ($json->optionsSql as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                        foreach ($json->options as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                    }
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name . "_label"];
                    }
                }
                if ($key === "type" && ($value === "suggest")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if ($json->defaultValue !== "") {
                        $json->data->value = $json->defaultValue;
                        $json->data->label = $json->defaultValue;
                        foreach ($json->optionsSql as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                        foreach ($json->options as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                    }
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        if (isset($this->fields["APP_DATA"][$json->name . "_label"])) {
                            $json->data->label = $this->fields["APP_DATA"][$json->name . "_label"];
                        }
                    }
                }
                if ($key === "type" && ($value === "radio")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if ($json->defaultValue !== "") {
                        foreach ($json->optionsSql as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                        foreach ($json->options as $os) {
                            if ($os->value === $json->defaultValue) {
                                $json->data->value = $os->value;
                                $json->data->label = $os->label;
                            }
                        }
                    }
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name . "_label"];
                    }
                }
                if ($key === "type" && ($value === "checkbox")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name];
                    }
                }
                if ($key === "type" && ($value === "checkgroup")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "[]";
                    if ($json->defaultValue !== "") {
                        $dataValue = array();
                        $dataLabel = array();
                        $dv = explode("|", $json->defaultValue);
                        foreach ($dv as $idv) {
                            foreach ($json->optionsSql as $os) {
                                if ($os->value === trim($idv)) {
                                    array_push($dataValue, $os->value);
                                    array_push($dataLabel, $os->label);
                                }
                            }
                            foreach ($json->options as $os) {
                                if ($os->value === trim($idv)) {
                                    array_push($dataValue, $os->value);
                                    array_push($dataLabel, $os->label);
                                }
                            }
                        }
                        $json->data->value = $dataValue;
                        $json->data->label = G::json_encode($dataLabel);
                    }
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name . "_label"];
                    }
                }
                if ($key === "type" && ($value === "datetime")) {
                    $json->data = new stdClass();
                    $json->data->value = "";
                    $json->data->label = "";
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        $json->data->value = $this->fields["APP_DATA"][$json->name];
                        $json->data->label = $this->fields["APP_DATA"][$json->name . "_label"];
                    }
                }
                if ($key === "type" && ($value === "file") && isset($this->fields["APP_DATA"]["APPLICATION"])) {
                    $oCriteria = new Criteria("workflow");
                    $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                    $oCriteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
                    $oCriteria->add(AppDocumentPeer::APP_UID, $this->fields["APP_DATA"]["APPLICATION"]);
                    $oCriteria->add(AppDocumentPeer::APP_DOC_FIELDNAME, $json->name);
                    $rs = AppDocumentPeer::doSelectRS($oCriteria);
                    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $links = array();
                    while ($rs->next()) {
                        $row = $rs->getRow();
                        array_push($links, "../cases/cases_ShowDocument?a=" . $row["APP_DOC_UID"] . "&v=" . $row["DOC_VERSION"]);
                    }
                    $json->data = new stdClass();
                    $json->data->value = $links;
                    $json->data->label = isset($this->fields["APP_DATA"][$json->name . "_label"]) ? $this->fields["APP_DATA"][$json->name . "_label"] : "[]";
                }
                if ($key === "type" && ($value === "file")) {
                    //todo
                    $oCriteria = new Criteria("workflow");
                    $oCriteria->addSelectColumn(ProcessVariablesPeer::INP_DOC_UID);
                    $oCriteria->add(ProcessVariablesPeer::VAR_NAME, $json->variable);
                    $rs = ProcessVariablesPeer::doSelectRS($oCriteria);
                    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $rs->next();
                    $row = $rs->getRow();
                    if (isset($row["INP_DOC_UID"])) {
                        $json->inputDocuments = array($row["INP_DOC_UID"]);
                    }
                }
                //synchronize var_label
                if ($key === "type" && ($value === "dropdown" || $value === "suggest")) {
                    if (isset($this->fields["APP_DATA"]["__VAR_CHANGED__"]) && in_array($json->name, explode(",", $this->fields["APP_DATA"]["__VAR_CHANGED__"]))) {
                        foreach ($json->optionsSql as $io) {
                            if ($json->data->value === $io->value) {
                                $json->data->label = $io->label;
                            }
                        }
                        foreach ($json->options as $io) {
                            if ($json->data->value === $io->value) {
                                $json->data->label = $io->label;
                            }
                        }
                        $_SESSION["TRIGGER_DEBUG"]["DATA"][] = Array("key" => $json->name . "_label", "value" => $json->data->label);
                    }
                }
                if ($key === "type" && ($value === "checkgroup")) {
                    if (isset($this->fields["APP_DATA"]["__VAR_CHANGED__"]) && in_array($json->name, explode(",", $this->fields["APP_DATA"]["__VAR_CHANGED__"]))) {
                        $dataValue = array();
                        $dataLabel = array();
                        $dv = array();
                        if (isset($this->fields["APP_DATA"][$json->name]))
                            $dv = $this->fields["APP_DATA"][$json->name];
                        foreach ($dv as $idv) {
                            foreach ($json->optionsSql as $os) {
                                if ($os->value === $idv) {
                                    array_push($dataValue, $os->value);
                                    array_push($dataLabel, $os->label);
                                }
                            }
                            foreach ($json->options as $os) {
                                if ($os->value === $idv) {
                                    array_push($dataValue, $os->value);
                                    array_push($dataLabel, $os->label);
                                }
                            }
                        }
                        $json->data->value = $dataValue;
                        $json->data->label = G::json_encode($dataLabel);
                        $_SESSION["TRIGGER_DEBUG"]["DATA"][] = Array("key" => $json->name . "_label", "value" => $json->data->label);
                    }
                }
                if ($key === "type" && ($value === "datetime")) {
                    if (isset($this->fields["APP_DATA"]["__VAR_CHANGED__"]) && in_array($json->name, explode(",", $this->fields["APP_DATA"]["__VAR_CHANGED__"]))) {
                        $json->data->label = $json->data->value;
                        $_SESSION["TRIGGER_DEBUG"]["DATA"][] = Array("key" => $json->name . "_label", "value" => $json->data->label);
                    }
                }
                //grid
                if ($key === "type" && ($value === "grid")) {
                    if (isset($this->fields["APP_DATA"][$json->name])) {
                        //rows
                        $rows = $this->fields["APP_DATA"][$json->name];
                        foreach ($rows as $keyRow => $row) {
                            //cells
                            $cells = array();
                            foreach ($json->columns as $column) {
                                //data
                                if ($column->type === "text" || $column->type === "textarea" || $column->type === "dropdown" || $column->type === "suggest" || $column->type === "datetime" || $column->type === "checkbox" || $column->type === "file" || $column->type === "link" || $column->type === "hidden") {
                                    array_push($cells, array(
                                        "value" => isset($row[$column->name]) ? $row[$column->name] : "",
                                        "label" => isset($row[$column->name . "_label"]) ? $row[$column->name . "_label"] : (isset($row[$column->name]) ? $row[$column->name] : "")
                                    ));
                                }
                            }
                            $rows[$keyRow] = $cells;
                        }
                        $json->rows = count($rows);
                        $json->data = $rows;
                    }
                    //todo compatibility 'columnWidth'
                    foreach ($json->columns as $column) {
                        if (!isset($column->columnWidth) && $column->type !== "hidden") {
                            $json->layout = "static";
                            $column->columnWidth = "";
                        }
                    }
                }
                //languages
                if ($this->lang === null && $key === "language" && isset($json->language)) {
                    $this->lang = $json->language;
                }
                if ($this->langs !== null) {
                    if (($key === "label" || $key === "title" || $key === "hint" || $key === "placeholder" || $key === "validateMessage" || $key === "alternateText" || $key === "comment" || $key === "alt") && isset($this->langs->{$this->lang})) {
                        $langs = $this->langs->{$this->lang}->Labels;
                        foreach ($langs as $langsValue) {
                            if (is_object($json) && $json->{$key} === $langsValue->msgid) {
                                $json->{$key} = $langsValue->msgstr;
                            }
                            if (is_array($json) && $json[$key] === $langsValue->msgid) {
                                $json[$key] = $langsValue->msgstr;
                            }
                        }
                    }
                }
                //EDIT,VIEW
                if (isset($this->fields["STEP_MODE"]) && $this->fields["STEP_MODE"] === "VIEW" && isset($json->mode)) {
                    $json->mode = "view";
                }
                if ($key === "type" && ($value === "form") && $this->records != null) {
                    foreach ($this->records as $ri) {
                        if ($json->id === $ri["DYN_UID"] && !isset($json->jsonUpdate)) {
                            $jsonUpdate = json_decode($ri["DYN_CONTENT"]);
                            $jsonUpdate = $jsonUpdate->items[0];
                            $jsonUpdate->colSpan = $json->colSpan;
                            $jsonUpdate->jsonUpdate = true;
                            $json = $jsonUpdate;
                            $this->jsonr($json);
                        }
                    }
                }
            }
        }
    }

    public function isResponsive()
    {
        return $this->record != null && $this->record["DYN_VERSION"] == 2 ? true : false;
    }

    public function printTracker()
    {
        ob_clean();

        $json = G::json_decode($this->record["DYN_CONTENT"]);

        foreach ($json->items[0]->items as $key => $value) {
            switch ($json->items[0]->items[$key][0]->type) {
                case "submit":
                    unset($json->items[0]->items[$key]);
                    break;
            }
        }

        $this->jsonr($json);

        $javascript = "
            <script type=\"text/javascript\">
                var jsondata = " . G::json_encode($json) . ";
                var pm_run_outside_main_app = \"\";
                var dyn_uid = \"" . $this->fields["CURRENT_DYNAFORM"] . "\";
                var __DynaformName__ = \"" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "\";
                var app_uid = \"" . $this->fields["APP_UID"] . "\";
                var prj_uid = \"" . $this->fields["PRO_UID"] . "\";
                var step_mode = \"\";
                var workspace = \"" . SYS_SYS . "\";
                var credentials = " . G::json_encode($this->credentials) . ";
                var filePost = \"\";
                var fieldsRequired = null;
                var triggerDebug = false;
                var sysLang = \"" . SYS_LANG . "\";
                $(window).load(function ()
                {
                    var data = jsondata;
                    data.items[0].mode = \"disabled\";

                    window.project = new PMDynaform.core.Project({
                        data: data,
                        keys: {
                            server: location.host,
                            projectId: prj_uid,
                            workspace: workspace
                        },
                        token: credentials,
                        submitRest: false
                    });
                    $(document).find(\"form\").submit(function (e) {
                        e.preventDefault();
                        return false;
                    });
                });
            </script>

            <div style=\"margin: 10px 20px 10px 0;\">
                <div style=\"float: right\"><a href=\"javascript: window.history.go(-1);\" style=\"text-decoration: none;\">&lt; " . G::LoadTranslation("ID_BACK") . "</a></div>
                <div style=\"clear: both\"></div>
            </div>
        ";

        $file = file_get_contents(PATH_HOME . "public_html" . PATH_SEP . "lib" . PATH_SEP . "pmdynaform" . PATH_SEP . "build" . PATH_SEP . "pmdynaform.html");
        $file = str_replace("{javascript}", $javascript, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit(0);
    }

    public function printView()
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $this->fields["APP_UID"] . "';\n" .
                "var prj_uid = '" . $this->fields["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "var triggerDebug = null;\n" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "$(window).load(function () {\n" .
                "    var data = jsondata;\n" .
                "    data.items[0].mode = 'disabled';\n" .
                "    window.project = new PMDynaform.core.Project({\n" .
                "        data: data,\n" .
                "        keys: {\n" .
                "            server: location.host,\n" .
                "            projectId: prj_uid,\n" .
                "            workspace: workspace\n" .
                "        },\n" .
                "        token: credentials,\n" .
                "        submitRest: false\n" .
                "    });\n" .
                "    $(document).find('form').submit(function (e) {\n" .
                "        e.preventDefault();\n" .
                "        return false;\n" .
                "    });\n" .
                "});\n" .
                "</script>\n";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit();
    }

    public function printEdit()
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        if (!isset($this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"])) {
            $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"] = "";
        }
        $msg = "";
        if (isset($_SESSION['G_MESSAGE_TYPE']) && isset($_SESSION['G_MESSAGE'])) {
            $color = "green";
            if ($_SESSION['G_MESSAGE_TYPE'] === "ERROR")
                $color = "red";
            if ($_SESSION['G_MESSAGE_TYPE'] === "WARNING")
                $color = "#C3C380";
            if ($_SESSION['G_MESSAGE_TYPE'] === "INFO")
                $color = "green";
            $msg = "<div style='background-color:" . $color . ";color: white;padding: 1px 2px 1px 5px;' class='userGroupTitle'>" . $_SESSION['G_MESSAGE_TYPE'] . ": " . $_SESSION['G_MESSAGE'] . "</div>";
            unset($_SESSION['G_MESSAGE_TYPE']);
            unset($_SESSION['G_MESSAGE']);
        }
        $title = $msg .
                "<table width='100%' align='center'>\n" .
                "    <tr class='userGroupTitle'>\n" .
                "        <td width='100%' align='center'>" . G::LoadTranslation('ID_CASE') . " #: " . $this->fields["APP_NUMBER"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . G::LoadTranslation('ID_TITLE') . ": " . $this->fields["APP_TITLE"] . "</td>\n" .
                "    </tr>\n" .
                "</table>\n";
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = '" . $this->fields["PM_RUN_OUTSIDE_MAIN_APP"] . "';\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = '" . $this->record["PRO_UID"] . "_" . $this->record["DYN_UID"] . "';\n" .
                "var app_uid = '" . $this->fields["APP_UID"] . "';\n" .
                "var prj_uid = '" . $this->fields["PRO_UID"] . "';\n" .
                "var step_mode = '" . $this->fields["STEP_MODE"] . "';\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = null;\n" .
                "var fieldsRequired = null;\n" .
                "var triggerDebug = " . ($this->fields["TRIGGER_DEBUG"] === 1 ? "true" : "false") . ";\n" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/cases_Step.js'></script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                ($this->fields["PRO_SHOW_MESSAGE"] === 1 ? '' : $title ) .
                "<div style='width:100%;padding:0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <img src='/images/bulletButtonLeft.gif' style='float:left;'>&nbsp;\n" .
                "    <a id='dyn_backward' href='" . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP"] . "' style='float:left;font-size:12px;line-height:1;margin:0px 0px 1px 5px;'>\n" .
                "    " . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["PREVIOUS_STEP_LABEL"] . "" .
                "    </a>\n" .
                "    <img src='/images/bulletButton.gif' style='float:right;'>&nbsp;\n" .
                "    <a id='dyn_forward' href='" . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP"] . "' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "    " . $this->fields["APP_DATA"]["__DYNAFORM_OPTIONS"]["NEXT_STEP_LABEL"] . "" .
                "    </a>\n" .
                "</div>";
        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit();
    }

    public function printEditSupervisor()
    {
        ob_clean();

        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);

        $msg = "";

        if (isset($_SESSION["G_MESSAGE_TYPE"]) && isset($_SESSION["G_MESSAGE"])) {
            $msg = "<div style=\"margin: 1.2em; border: 1px solid #3C763D; padding: 0.5em; background: #B2D3B3;\"><strong>" . G::LoadTranslation("ID_INFO") . "</strong>: " . $_SESSION["G_MESSAGE"] . "</div>";

            unset($_SESSION["G_MESSAGE_TYPE"]);
            unset($_SESSION["G_MESSAGE"]);
        }

        $javascrip = "
        <script type=\"text/javascript\">
            var jsondata = " . G::json_encode($json) . ";
            var pm_run_outside_main_app = null;
            var dyn_uid = \"" . $this->fields["CURRENT_DYNAFORM"] . "\";
            var __DynaformName__ = \"" . $this->fields["PRO_UID"] . "_" . $this->fields["CURRENT_DYNAFORM"] . "\";
            var app_uid = \"" . $this->fields["APP_UID"] . "\";
            var prj_uid = \"" . $this->fields["PRO_UID"] . "\";
            var step_mode = null;
            var workspace = \"" . SYS_SYS . "\";
            var credentials = " . G::json_encode($this->credentials) . ";
            var filePost = \"cases_SaveDataSupervisor?UID=" . $this->fields["CURRENT_DYNAFORM"] . "\";
            var fieldsRequired = null;
            var triggerDebug   = null;
            var sysLang = \"" . SYS_LANG . "\";
        </script>

        <script type=\"text/javascript\" src=\"/jscore/cases/core/pmDynaform.js\"></script>

        <div>
            $msg
            <div style=\"display: none;\">
                <a id=\"dyn_forward\" href=\"javascript:;\"></a>
            </div>
        </div>
        ";

        $file = file_get_contents(PATH_HOME . "public_html" . PATH_SEP . "lib" . PATH_SEP . "pmdynaform" . PATH_SEP . "build" . PATH_SEP . "pmdynaform.html");
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit(0);
    }

    public function printWebEntry($filename)
    {
        ob_clean();
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = null;\n" .
                "var app_uid = null;\n" .
                "var prj_uid = '" . $this->record["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = '" . $filename . "';\n" .
                "var fieldsRequired = " . G::json_encode(array()) . ";\n" .
                "var triggerDebug = null;\n" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                "<div style='width:100%;padding: 0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <img src='/images/bulletButton.gif' style='float:right;'>&nbsp;\n" .
                "    <a id='dyn_forward' href='' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "        Next Step\n" .
                "    </a>\n" .
                "</div>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit();
    }

    public function printABE($filename, $record)
    {
        ob_clean();
        $this->record = $record;
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = null;\n" .
                "var app_uid = null;\n" .
                "var prj_uid = '" . $this->record["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var filePost = '" . $filename . "';\n" .
                "var fieldsRequired = " . G::json_encode(array()) . ";\n" .
                "var triggerDebug = null;\n" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                "<div style='width:100%;padding: 0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "    <a id='dyn_forward' href='' style='float:right;font-size:12px;line-height:1;margin:0px 5px 1px 0px;'>\n" .
                "    </a>\n" .
                "</div>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit();
    }

    public function printPmDynaform($js = "")
    {
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "var jsonData = " . G::json_encode($json) . ";" .
                $js .
                "</script>";

        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        echo $file;
        exit();
    }

    public function printPmDynaformAbe($record)
    {
        ob_clean();
        $this->record = $record;
        $json = G::json_decode($this->record["DYN_CONTENT"]);
        $this->jsonr($json);
        $javascrip = "" .
                "<script type='text/javascript'>\n" .
                "var jsondata = " . G::json_encode($json) . ";\n" .
                "var pm_run_outside_main_app = null;\n" .
                "var dyn_uid = '" . $this->fields["CURRENT_DYNAFORM"] . "';\n" .
                "var __DynaformName__ = null;\n" .
                "var app_uid = null;\n" .
                "var prj_uid = '" . $this->record["PRO_UID"] . "';\n" .
                "var step_mode = null;\n" .
                "var workspace = '" . SYS_SYS . "';\n" .
                "var credentials = " . G::json_encode($this->credentials) . ";\n" .
                "var fieldsRequired = " . G::json_encode(array()) . ";\n" .
                "var triggerDebug = null;\n" .
                "var sysLang = '" . SYS_LANG . "';\n" .
                "</script>\n" .
                "<script type='text/javascript' src='/jscore/cases/core/pmDynaform.js'></script>\n" .
                "<div style='width:100%;padding: 0px 10px 0px 10px;margin:15px 0px 0px 0px;'>\n" .
                "</div>";
        $file = file_get_contents(PATH_HOME . 'public_html/lib/pmdynaform/build/pmdynaform.html');
        $file = str_replace("{javascript}", $javascrip, $file);
        $file = str_replace("{sys_skin}", SYS_SKIN, $file);
        return $file;
    }

    public function synchronizeVariable($processUid, $newVariable, $oldVariable)
    {
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(DynaformPeer::DYN_UID);
        $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $criteria->add(DynaformPeer::PRO_UID, $processUid, Criteria::EQUAL);
        $rsCriteria = DynaformPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($rsCriteria->next()) {
            $aRow = $rsCriteria->getRow();
            $json = G::json_decode($aRow['DYN_CONTENT']);
            $this->jsons($json, $newVariable, $oldVariable);
            $json2 = G::json_encode($json);
            //update dynaform
            if ($json2 !== $aRow['DYN_CONTENT']) {
                $con = Propel::getConnection(DynaformPeer::DATABASE_NAME);
                $con->begin();
                $oPro = DynaformPeer::retrieveByPk($aRow["DYN_UID"]);
                $oPro->setDynContent($json2);
                $oPro->save();
                $con->commit();
            }
        }
    }

    private function jsons(&$json, $newVariable, $oldVariable)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $this->jsons($value, $newVariable, $oldVariable);
            }
            if (!$sw1 && !$sw2) {
                if ($key === "variable" && $json->variable === $oldVariable["VAR_NAME"]) {
                    $json->variable = $newVariable["VAR_NAME"];
                    if (isset($json->dataType))
                        $json->dataType = $newVariable["VAR_FIELD_TYPE"];
                    if (isset($json->name))
                        $json->name = $newVariable["VAR_NAME"];
                    if (isset($json->dbConnection) && $json->dbConnection === $oldVariable["VAR_DBCONNECTION"])
                        $json->dbConnection = $newVariable["VAR_DBCONNECTION"];
                    if (isset($json->dbConnectionLabel) && $json->dbConnectionLabel === $oldVariable["VAR_DBCONNECTION_LABEL"])
                        $json->dbConnectionLabel = $newVariable["VAR_DBCONNECTION_LABEL"];
                    if (isset($json->sql) && $json->sql === $oldVariable["VAR_SQL"])
                        $json->sql = $newVariable["VAR_SQL"];
                    if (isset($json->options) && G::json_encode($json->options) === $oldVariable["VAR_ACCEPTED_VALUES"]) {
                        $json->options = G::json_decode($newVariable["VAR_ACCEPTED_VALUES"]);
                    }
                }
            }
        }
    }

    public function synchronizeInputDocument($processUid, $inputDocument)
    {
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(DynaformPeer::DYN_UID);
        $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $criteria->add(DynaformPeer::PRO_UID, $processUid, Criteria::EQUAL);
        $rsCriteria = DynaformPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($rsCriteria->next()) {
            $aRow = $rsCriteria->getRow();
            $json = G::json_decode($aRow['DYN_CONTENT']);
            $this->jsonsid($json, $inputDocument);
            $json2 = G::json_encode($json);
            //update dynaform
            if ($json2 !== $aRow['DYN_CONTENT']) {
                $con = Propel::getConnection(DynaformPeer::DATABASE_NAME);
                $con->begin();
                $oPro = DynaformPeer::retrieveByPk($aRow["DYN_UID"]);
                $oPro->setDynContent($json2);
                $oPro->save();
                $con->commit();
            }
        }
    }

    private function jsonsid(&$json, $inputDocument)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $this->jsonsid($value, $inputDocument);
            }
            if (!$sw1 && !$sw2) {
                if ($key === "type" && $json->type === "file" && $json->variable !== "") {
                    $a = new Criteria("workflow");
                    $a->addSelectColumn(ProcessVariablesPeer::INP_DOC_UID);
                    $a->add(ProcessVariablesPeer::VAR_NAME, $json->variable, Criteria::EQUAL);
                    $ds = DynaformPeer::doSelectRS($a);
                    $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $ds->next();
                    $row = $ds->getRow();
                    if (isset($row) && $row["INP_DOC_UID"] === $inputDocument["INP_DOC_UID"]) {
                        if (isset($json->size)) {
                            $json->size = $inputDocument["INP_DOC_MAX_FILESIZE"];
                        }
                        if (isset($json->sizeUnity)) {
                            $json->sizeUnity = $inputDocument["INP_DOC_MAX_FILESIZE_UNIT"];
                        }
                        if (isset($json->extensions)) {
                            $json->extensions = $inputDocument["INP_DOC_TYPE_FILE"];
                        }
                    }
                }
            }
        }
    }

    public function isUsed($processUid, $variable)
    {
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(DynaformPeer::DYN_UID);
        $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $criteria->add(DynaformPeer::PRO_UID, $processUid, Criteria::EQUAL);
        $rsCriteria = DynaformPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($rsCriteria->next()) {
            $aRow = $rsCriteria->getRow();
            $json = G::json_decode($aRow['DYN_CONTENT']);
            if ($this->jsoni($json, $variable)) {
                return $aRow['DYN_UID'];
            }
        }
        return false;
    }

    private function jsoni(&$json, $variable)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                if ($this->jsoni($value, $variable)) {
                    return true;
                }
            }
            if (!$sw1 && !$sw2) {
                if ($key === "variable" && $json->variable === $variable["var_name"]) {
                    return true;
                }
            }
        }
        return false;
    }

    public function searchField($dyn_uid, $field_id)
    {
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
        $a->add(DynaformPeer::DYN_UID, $dyn_uid, Criteria::EQUAL);
        $ds = ProcessPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $ds->next();
        $row = $ds->getRow();
        $json = G::json_decode($row["DYN_CONTENT"]);
        return $this->jsonsf($json, $field_id);
    }

    private function jsonsf(&$json, $id)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $val = $this->jsonsf($value, $id);
                if ($val !== null)
                    return $val;
            }
            if (!$sw1 && !$sw2) {
                if ($key === "id" && $id === $value) {
                    return $json;
                }
            }
        }
        return null;
    }

    public function downloadLanguage($dyn_uid, $lang)
    {
        if ($lang === "en") {
            $a = new Criteria("workflow");
            $a->addSelectColumn(DynaformPeer::DYN_CONTENT);
            $a->add(DynaformPeer::DYN_UID, $dyn_uid, Criteria::EQUAL);
            $ds = ProcessPeer::doSelectRS($a);
            $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $ds->next();
            $row = $ds->getRow();
            if ($row["DYN_CONTENT"] !== null && $row["DYN_CONTENT"] !== "") {
                $json = \G::json_decode($row["DYN_CONTENT"]);
                $this->jsonl($json);
            }
            $string = "";
            $string = $string . "msgid \"\"\n";
            $string = $string . "msgstr \"\"\n";
            $string = $string . "\"Project-Id-Version: PM 4.0.1\\n\"\n";
            $string = $string . "\"POT-Creation-Date: \\n\"\n";
            $string = $string . "\"PO-Revision-Date: 2010-12-02 11:44+0100 \\n\"\n";
            $string = $string . "\"Last-Translator: Colosa<colosa@colosa.com>\\n\"\n";
            $string = $string . "\"Language-Team: Colosa Developers Team <developers@colosa.com>\\n\"\n";
            $string = $string . "\"MIME-Version: 1.0\\n\"\n";
            $string = $string . "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
            $string = $string . "\"Content-Transfer_Encoding: 8bit\\n\"\n";
            $string = $string . "\"X-Poedit-Language: English\\n\"\n";
            $string = $string . "\"X-Poedit-Country: United States\\n\"\n";
            $string = $string . "\"X-Poedit-SourceCharset: utf-8\\n\"\n";
            $string = $string . "\"Content-Transfer-Encoding: 8bit\\n\"\n";
            $string = $string . "\"File-Name: processmaker.en.po\\n\"\n\n";

            $n = count($this->dyn_conten_labels);
            for ($i = 0; $i < $n; $i++) {
                $string = $string . "msgid \"" . $this->dyn_conten_labels[$i] . "\"\n";
                $string = $string . "msgstr \"" . $this->dyn_conten_labels[$i] . "\"\n\n";
            }
            return array("labels" => $string, "lang" => $lang);
        } else {
            $a = new Criteria("workflow");
            $a->addSelectColumn(DynaformPeer::DYN_LABEL);
            $a->add(DynaformPeer::DYN_UID, $dyn_uid, Criteria::EQUAL);
            $ds = ProcessPeer::doSelectRS($a);
            $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $ds->next();
            $row = $ds->getRow();
            $data = G::json_decode($row["DYN_LABEL"]);
            $string = "";
            $string = $string . "msgid \"\"\n";
            $string = $string . "msgstr \"\"\n";
            foreach ($data->{$lang} as $key => $value) {
                if (is_string($value)) {
                    $string = $string . "\"" . $key . ": " . $value . "\\n\"\n";
                }
            }
            $string = $string . "\n";
            foreach ($data->{$lang}->Labels as $key => $value) {
                $string = $string . "msgid \"" . $value->msgid . "\"\n";
                $string = $string . "msgstr \"" . $value->msgstr . "\"\n\n";
            }
            return array("labels" => $string, "lang" => $lang);
        }
    }

    public function uploadLanguage($dyn_uid)
    {
        if (!isset($_FILES["LANGUAGE"])) {
            throw new Exception(G::LoadTranslation("ID_ERROR_UPLOADING_FILENAME"));
        }
        if (pathinfo($_FILES["LANGUAGE"]["name"], PATHINFO_EXTENSION) != "po") {
            throw new Exception(G::LoadTranslation("ID_FILE_UPLOAD_INCORRECT_EXTENSION"));
        }
        $translation = array();
        G::LoadSystem('i18n_po');
        $i18n = new i18n_PO($_FILES["LANGUAGE"]["tmp_name"]);
        $i18n->readInit();
        while ($rowTranslation = $i18n->getTranslation()) {
            array_push($translation, $rowTranslation);
        }
        $name = $_FILES["LANGUAGE"]["name"];
        $name = explode(".", $name);
        if (isset($name[1]) && isset($name[2]) && $name[1] . "." . $name[2] === "en.po") {
            return;
        }
        $content = $i18n->getHeaders();
        $content["File-Name"] = $_FILES["LANGUAGE"]["name"];
        $content["Labels"] = $translation;

        $con = Propel::getConnection(DynaformPeer::DATABASE_NAME);
        $con->begin();
        $oPro = DynaformPeer::retrieveByPk($dyn_uid);

        $dyn_labels = new stdClass();
        if ($oPro->getDynLabel() !== null && $oPro->getDynLabel() !== "") {
            $dyn_labels = G::json_decode($oPro->getDynLabel());
        }
        $dyn_labels->$name[count($name) - 2] = $content;

        $oPro->setDynLabel(G::json_encode($dyn_labels));
        $oPro->save();
        $con->commit();
    }

    public function listLanguage($dyn_uid)
    {
        $list = array();
        $a = new Criteria("workflow");
        $a->addSelectColumn(DynaformPeer::DYN_LABEL);
        $a->add(DynaformPeer::DYN_UID, $dyn_uid, Criteria::EQUAL);
        $ds = ProcessPeer::doSelectRS($a);
        $ds->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $ds->next();
        $row = $ds->getRow();

        if ($row["DYN_LABEL"] === null || $row["DYN_LABEL"] === "") {
            return $list;
        }

        $dyn_label = \G::json_decode($row["DYN_LABEL"]);
        foreach ($dyn_label as $key => $value) {
            array_push($list, array(
                "Lang" => $key,
                "File-Name" => isset($value->{"File-Name"}) ? $value->{"File-Name"} : "",
                "Project-Id-Version" => isset($value->{"Project-Id-Version"}) ? $value->{"Project-Id-Version"} : "",
                "POT-Creation-Date" => isset($value->{"POT-Creation-Date"}) ? $value->{"POT-Creation-Date"} : "",
                "PO-Revision-Date" => isset($value->{"PO-Revision-Date"}) ? $value->{"PO-Revision-Date"} : "",
                "Last-Translator" => isset($value->{"Last-Translator"}) ? $value->{"Last-Translator"} : "",
                "Language-Team" => isset($value->{"Language-Team"}) ? $value->{"Language-Team"} : "",
                "MIME-Version" => isset($value->{"MIME-Version"}) ? $value->{"MIME-Version"} : "",
                "Content-Type" => isset($value->{"Content-Type"}) ? $value->{"Content-Type"} : "",
                "Content-Transfer_Encoding" => isset($value->{"Content-Transfer_Encoding"}) ? $value->{"Content-Transfer_Encoding"} : "",
                "X-Poedit-Language" => isset($value->{"X-Poedit-Language"}) ? $value->{"X-Poedit-Language"} : "",
                "X-Poedit-Country" => isset($value->{"X-Poedit-Country"}) ? $value->{"X-Poedit-Country"} : "",
                "X-Poedit-SourceCharset" => isset($value->{"X-Poedit-SourceCharset"}) ? $value->{"X-Poedit-SourceCharset"} : "",
                "Content-Transfer-Encoding" => isset($value->{"Content-Transfer-Encoding"}) ? $value->{"Content-Transfer-Encoding"} : ""
            ));
        }
        return $list;
    }

    private $dyn_conten_labels = array();

    private function jsonl(&$json)
    {
        foreach ($json as $key => $value) {
            $sw1 = is_array($value);
            $sw2 = is_object($value);
            if ($sw1 || $sw2) {
                $this->jsonl($value);
            }
            if (!$sw1 && !$sw2) {
                if ($key === "label") {
                    array_push($this->dyn_conten_labels, $json->label);
                }
                if ($key === "hint") {
                    array_push($this->dyn_conten_labels, $json->hint);
                }
                if ($key === "placeholder") {
                    array_push($this->dyn_conten_labels, $json->placeholder);
                }
                if ($key === "validateMessage") {
                    array_push($this->dyn_conten_labels, $json->validateMessage);
                }
                if ($key === "alternateText") {
                    array_push($this->dyn_conten_labels, $json->alternateText);
                }
                if ($key === "comment") {
                    array_push($this->dyn_conten_labels, $json->comment);
                }
                if ($key === "alt") {
                    array_push($this->dyn_conten_labels, $json->alt);
                }
            }
        }
    }

    public function deleteLanguage($dyn_uid, $lang)
    {
        $con = Propel::getConnection(DynaformPeer::DATABASE_NAME);
        $con->begin();
        $oPro = DynaformPeer::retrieveByPk($dyn_uid);

        $dyn_labels = \G::json_decode($oPro->getDynLabel());
        unset($dyn_labels->{$lang});

        $oPro->setDynLabel(G::json_encode($dyn_labels));
        $oPro->save();
        $con->commit();
    }

    private function clientToken()
    {
        $client = $this->getClientCredentials();
        $authCode = $this->getAuthorizationCode($client);


        $request = array(
            'grant_type' => 'authorization_code',
            'code' => $authCode
        );
        $server = array(
            'REQUEST_METHOD' => 'POST'
        );
        $headers = array(
            "PHP_AUTH_USER" => $client['CLIENT_ID'],
            "PHP_AUTH_PW" => $client['CLIENT_SECRET'],
            "Content-Type" => "multipart/form-data;",
            "Authorization" => "Basic " . base64_encode($client['CLIENT_ID'] . ":" . $client['CLIENT_SECRET'])
        );

        $request = new \OAuth2\Request(array(), $request, array(), array(), array(), $server, null, $headers);
        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $response = $oauthServer->getServer()->handleTokenRequest($request);
        $clientToken = $response->getParameters();
        $clientToken["client_id"] = $client['CLIENT_ID'];
        $clientToken["client_secret"] = $client['CLIENT_SECRET'];

        return $clientToken;
    }

    protected $clientId = 'x-pm-local-client';

    protected function getClientCredentials()
    {
        $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails($this->clientId);
    }

    protected function getAuthorizationCode($client)
    {
        \ProcessMaker\Services\OAuth2\Server::setDatabaseSource($this->getDsn());
        \ProcessMaker\Services\OAuth2\Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);

        return $code;
    }

    private function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

}
