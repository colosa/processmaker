<?php

namespace ProcessMaker\Util;

use Configurations;
use Criteria;
use ResultSet;
use FieldsPeer;
use ReportTablePeer;
use CaseConsolidatedCorePeer;
use ConsolidatedCases;
use AdditionalTablesPeer;
use PmTable;
use ReportVarPeer;
use AdditionalTables;
use stdClass;

/**
 * This class regenerates the 'Propel' classes that are necessary for the
 * administration of a 'Report Table', this is caused by the import of processes
 * where the data directory of ProcessMaker has different routes.
 */
class FixReferencePath
{
    private $modeDebug = false;
    private $resumeDebug = "";

    /**
     * Get property modeDebug.
     *
     * @return boolean
     */
    public function getModeDebug()
    {
        return $this->modeDebug;
    }

    /**
     * Set property modeDebug.
     *
     * @param boolean $modeDebug
     */
    public function setModeDebug($modeDebug)
    {
        $this->modeDebug = $modeDebug;
    }

    /**
     * Get property resumeDebug.
     *
     * @return string
     */
    public function getResumeDebug()
    {
        return $this->resumeDebug;
    }

    /**
     * Set property resumeDebug.
     *
     * @param string $resumeDebug
     */
    public function setResumeDebug($resumeDebug)
    {
        $this->resumeDebug = $resumeDebug;
    }

    /**
     * Find all PHP type files recursively.
     * The '$pathData' argument is the path to be replaced with the path found
     * as incorrect.
     *
     * @param string $directory
     * @param string $pathData
     * @return void
     */
    public function runProcess($directory, $pathData)
    {
        try {
            //This variable is not defined and does not involve its value in this
            //task, it is removed at the end of the method.
            $_SERVER["REQUEST_URI"] = "";
            if (!defined("SYS_SKIN")) {
                $conf = new Configurations();
                define("SYS_SKIN", $conf->getConfiguration('SKIN_CRON', ''));
            }

            $criteria = new Criteria("workflow");
            $criteria->addSelectColumn(ReportTablePeer::REP_TAB_UID);
            $criteria->addSelectColumn(CaseConsolidatedCorePeer::TAS_UID);
            $criteria->addSelectColumn(ReportTablePeer::REP_TAB_NAME);
            $criteria->addJoin(ReportTablePeer::REP_TAB_UID, CaseConsolidatedCorePeer::REP_TAB_UID, Criteria::JOIN);
            $criteria->add(CaseConsolidatedCorePeer::CON_STATUS, "ACTIVE", Criteria::EQUAL);
            $doSelect = ReportTablePeer::doSelectRS($criteria);
            $doSelect->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $consolidatedCases = new ConsolidatedCases();
            while ($doSelect->next()) {
                $row = $doSelect->getRow();
                $fields = $this->getReportTableFields($row["REP_TAB_UID"]);
                list($fields, $outFields) = $consolidatedCases->buildReportVariables($fields);
                try {
                    $this->regeneratePropelClasses($row["REP_TAB_NAME"], $row["REP_TAB_NAME"], $fields, $row["TAS_UID"]);
                    $this->outVerboseln("* Regenerate classes for table: " . $row["REP_TAB_NAME"]);
                } catch (Exception $e) {
                    CLI::logging(CLI::error("Error:" . "Error in regenerate classes for table: " . $row["REP_TAB_NAME"] . ". " . $e));
                }
            }

            $criteria = new Criteria("workflow");
            $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
            $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
            $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_CLASS_NAME);
            $criteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);
            $doSelect = AdditionalTablesPeer::doSelectRS($criteria);
            $doSelect->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($doSelect->next()) {
                $row = $doSelect->getRow();
                $fields = $this->getAdditionalTablesFields($row["ADD_TAB_UID"]);
                try {
                    $pmTable = new PmTable($row["ADD_TAB_NAME"]);
                    $pmTable->setDbConfigAdapter("mysql");
                    $pmTable->setColumns($fields);
                    $pmTable->prepare();
                    $pmTable->preparePropelIniFile();
                    $pmTable->buildSchema();
                    $pmTable->phingbuildModel();
                    $this->outVerboseln("* Regenerate classes for table: " . $row["ADD_TAB_NAME"]);
                } catch (Exception $e) {
                    CLI::logging(CLI::error("Error:" . "Error in regenerate classes for table: " . $row["ADD_TAB_NAME"] . ". " . $e));
                }
            }

            unset($_SERVER["REQUEST_URI"]);
        } catch (Exception $e) {
            CLI::logging(CLI::error("Error:" . "Error in regenerate classes files, proceed to regenerate manually: " . $e));
        }
    }

    /**
     * Gets the fields of the 'Report Table'.
     *
     * @param string $repTabUid
     * @return array
     */
    public function getReportTableFields($repTabUid)
    {
        $fields = array();
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(ReportVarPeer::REP_VAR_NAME);
        $criteria->addSelectColumn(ReportVarPeer::REP_VAR_TYPE);
        $criteria->add(ReportVarPeer::REP_TAB_UID, $repTabUid, Criteria::EQUAL);
        $doSelect = ReportVarPeer::doSelectRS($criteria);
        $doSelect->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($doSelect->next()) {
            $row = $doSelect->getRow();
            $fields[] = $row['REP_VAR_NAME'] . '-' . $row['REP_VAR_TYPE'];
        }
        return $fields;
    }

    /**
     * Gets the fields of the 'Additional Table'.
     *
     * @param string $addTabUid
     * @return object
     */
    public function getAdditionalTablesFields($addTabUid)
    {
        $fields = array();
        $criteria = new Criteria("workflow");
        $criteria->add(FieldsPeer::ADD_TAB_UID, $addTabUid);
        $doSelect = FieldsPeer::doSelectRS($criteria);
        $doSelect->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        while ($doSelect->next()) {
            $row = $doSelect->getRow();
            $object = new stdClass();
            $object->field_index = $row["FLD_INDEX"];
            $object->field_name = $row["FLD_NAME"];
            $object->field_description = $row["FLD_DESCRIPTION"];
            $object->field_type = $row["FLD_TYPE"];
            $object->field_size = $row["FLD_SIZE"];
            $object->field_null = $row["FLD_NULL"];
            $object->field_autoincrement = $row["FLD_AUTO_INCREMENT"];
            $object->field_key = $row["FLD_KEY"];
            $fields[] = $object;
        }
        return $fields;
    }

    /**
     * Regenerate 'Propel' classes for 'Report Tables'. The name of the 'Report Table',
     * the fields and the related task are required.
     *
     * @param string $repTabName
     * @param array $fields
     * @param string $guid
     * @return void
     */
    public function regeneratePropelClasses($repTabName, $className, $fields, $guid)
    {
        $sourcePath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;

        @unlink($sourcePath . $className . '.php');
        @unlink($sourcePath . $className . 'Peer.php');
        @unlink($sourcePath . PATH_SEP . 'map' . PATH_SEP . $className . 'MapBuilder.php');
        @unlink($sourcePath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $className . '.php');
        @unlink($sourcePath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $className . 'Peer.php');

        $additionalTables = new AdditionalTables();
        $additionalTables->createPropelClasses($repTabName, $className, $fields, $guid);
    }

    /**
     * Display the output found, the message is not displayed if the value of the
     * 'modeVerbose' property is false.
     *
     * @param string $message
     * @return void
     */
    private function outVerbose($message)
    {
        $this->resumeDebug = $this->resumeDebug . $message;
        if ($this->modeDebug === true) {
            echo $message;
        }
    }

    /**
     * Shows on the screen the output found with line break.
     *
     * @param string $message
     * @return void
     */
    private function outVerboseln($message)
    {
        $this->outVerbose($message . "\n");
    }
}

