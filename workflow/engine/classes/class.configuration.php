<?php

/**
 * class.configuration.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
 *
 */
//
// It works with the table CONFIGURATION in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


/**
 * ProcessConfiguration - ProcessConfiguration class
 *
 * @author David S. Callizaya S.
 * @copyright 2007 COLOSA
 */
require_once 'classes/model/Configuration.php';

/**
 * Extends Configuration
 *
 *
 * @copyright 2007 COLOSA
 * @version Release: @package_version@
 * @package workflow.engine.ProcessMaker
 */
class Configurations // extends Configuration
{

    public $aConfig        = array();
    private $Configuration = null;
    private $UserConfig    = null;

    /**
     * Set Configurations
     *
     * @return void
     */
    public function Configurations()
    {
        $this->Configuration = new Configuration();
    }

    /**
     * arrayClone
     *
     * @param array &$object Source array
     * @param array &$cloneObject Array duplicate
     * @return void
     */
    public function arrayClone(&$object, &$cloneObject)
    {
        if (is_array($object)) {
            foreach ($object as $k => $v) {
                $cloneObject[$k] = null;
                $this->arrayClone($object[$k], $cloneObject[$k]);
            }
        } else {
            if (is_object( $object )) {
            } else {
                $cloneObject = null;
            }
        }
    }

    /**
     * configObject
     *
     * @param object &$object
     * @param array &$from
     * @return void
     */
    public function configObject(&$object, &$from)
    {
        if (!(is_object($object) || is_array($object))) {
            return;
        }

        if (!isset($from)) {
            $from = &$this->aConfig;
        }

        foreach ($from as $k => $v) {
            if (isset($v) && array_key_exists($k, $object)) {
                if (is_object($v)) {
                    throw new Exception('Object is not permited inside configuration array.');
                }

                if (is_object($object)) {
                    if (is_array($v)) {
                        $this->configObject($object->{$k}, $v);
                    } else {
                        $object->{$k} = $v;
                    }
                } else {
                    if (is_array($object)) {
                        if (is_array($v)) {
                            $this->configObject($object[$k], $v);
                        } else {
                            $object[$k] = $v;
                        }
                    }
                }
            }
        }
    }

    /**
     * loadConfig
     *
     * @param object &$object
     * @param string $cfg
     * @param object $obj
     * @param string $pro
     * @param string $usr
     * @param string $app
     * @return void
     */
    public function loadConfig(&$object, $cfg, $obj = '', $pro = '', $usr = '', $app = '')
    {
        $this->load($cfg, $obj, $pro, $usr, $app);
        $this->configObject($object, $this->aConfig);
    }

    /**
     * loadConf
     *
     * @param string $cfg
     * @param object $obj
     * @param string $pro
     * @param string $usr
     * @param string $app
     * @return void
     */
    public function load($cfg, $obj = '', $pro = '', $usr = '', $app = '')
    {
        $this->Fields = array();

        try {
            $this->Fields = $this->Configuration->load($cfg, $obj, $pro, $usr, $app);
        } catch (Exception $e) {

        } // the configuration does not exist


        if (isset($this->Fields['CFG_VALUE'])) {
            $this->aConfig = unserialize($this->Fields['CFG_VALUE']);
        }

        if (!is_array($this->aConfig)) {
            $this->aConfig = Array();
        }

        return $this->aConfig;
    }

    /**
     * saveConfig
     *
     * @param object &$object
     * @param array &$from
     * @return void
     */
    public function saveConfig($cfg, $obj, $pro = '', $usr = '', $app = '')
    {
        $aFields = array('CFG_UID' => $cfg, 'OBJ_UID' => $obj, 'PRO_UID' => $pro, 'USR_UID' => $usr, 'APP_UID' => $app, 'CFG_VALUE' => serialize($this->aConfig)
        );
        if ($this->Configuration->exists($cfg, $obj, $pro, $usr, $app)) {
            $this->Configuration->update($aFields);
        } else {
            $this->Configuration->create($aFields);
            $this->Configuration->update($aFields);
        }
    }

    /**
     * saveObject
     *
     * @param object &$object
     * @param array &$from
     * @return void
     */
    public function saveObject(&$object, $cfg, $obj, $pro = '', $usr = '', $app = '')
    {
        $aFields = array('CFG_UID' => $cfg, 'OBJ_UID' => $obj, 'PRO_UID' => $pro, 'USR_UID' => $usr, 'APP_UID' => $app, 'CFG_VALUE' => serialize(array(&$object
            ))
        );
        if ($this->Configuration->exists($cfg, $obj, $pro, $usr, $app)) {
            $this->Configuration->update($aFields);
        } else {
            $this->Configuration->create($aFields);
            $this->Configuration->update($aFields);
        }
    }

    /**
     * loadObject
     * this function is deprecated, we dont know why return an object, use the function getConfiguration below
     *
     * @param string $cfg
     * @param object $obj
     * @param string $pro
     * @param string $usr
     * @param string $app
     * @return void
     */
    public function loadObject($cfg, $obj, $pro = '', $usr = '', $app = '')
    {
        $objectContainer = array((object) array());
        $this->Fields = array();
        if ($this->Configuration->exists($cfg, $obj, $pro, $usr, $app)) {
            $this->Fields = $this->Configuration->load($cfg, $obj, $pro, $usr, $app);
        } else {
            return $objectContainer[0];
        }

        if (isset($this->Fields['CFG_VALUE'])) {
            $objectContainer = unserialize($this->Fields['CFG_VALUE']);
        }
        if (!is_array($objectContainer) || sizeof($objectContainer) != 1) {
            return (object) array();
        } else {
            return $objectContainer[0];
        }
    }

    /**
     * getConfiguration
     *
     * @param string $cfg
     * @param object $obj
     * @param string $pro
     * @param string $usr
     * @param string $app
     * @return void
     */
    public function getConfiguration($cfg, $obj, $pro = '', $usr = '', $app = '')
    {
        try {
            $oCfg = ConfigurationPeer::retrieveByPK($cfg, $obj, $pro, $usr, $app);
            if (!is_null($oCfg)) {
                $row = $oCfg->toArray(BasePeer::TYPE_FIELDNAME);
                $result = unserialize($row['CFG_VALUE']);
                if (is_array($result) && sizeof($result) == 1) {
                    $arrayKeys = Array_keys($result);
                    return $result[$arrayKeys[0]];
                } else {
                    return $result;
                }
            } else {
                return null;
            }
        } catch (Exception $oError) {
            return null;
        }
    }

    /**
     * usersNameFormat
     *
     * @author Enrique Ponce de Leon enrique@colosa.com
     * @param string $username
     * @param string $firstname
     * @param string $lastname
     * @return string User Name Well-Formatted
     */
    public function usersNameFormat($username, $firstname, $lastname)
    {
        try {
            if (!isset($this->UserConfig)) {
                $this->UserConfig = $this->getConfiguration('ENVIRONMENT_SETTINGS', '');
            }
            if (isset($this->UserConfig['format'])) {
                $aux = '';
                $aux = str_replace('@userName', $username, $this->UserConfig['format']);
                $aux = str_replace('@firstName', $firstname, $aux);
                $aux = str_replace('@lastName', $lastname, $aux);
                return $aux;
            } else {
                return $username;
            }
        } catch (Exception $oError) {
            return null;
        }
    }

    /**
     * getFormats
     *
     * @author Enrique Ponce de Leon enrique@colosa.com
     * @return FORMATS array
     */
    public function getFormats()
    {
        if (!isset($this->UserConfig)) {
            $this->UserConfig = $this->getConfiguration("ENVIRONMENT_SETTINGS", "");
        }

        //Setting defaults
        if (!isset($this->UserConfig["format"])) {
            $this->UserConfig["format"] = "@lastName, @firstName (@userName)";
        }

        if (!isset($this->UserConfig["dateFormat"])) {
            $this->UserConfig["dateFormat"] = "Y-m-d H:i:s";
        }

        if (!isset($this->UserConfig["startCaseHideProcessInf"])) {
            $this->UserConfig["startCaseHideProcessInf"] = false;
        }

        if (!isset($this->UserConfig["casesListDateFormat"])) {
            $this->UserConfig["casesListDateFormat"] = "Y-m-d H:i:s";
        }

        if (!isset($this->UserConfig["casesListRowNumber"])) {
            $this->UserConfig["casesListRowNumber"] = 25;
        }

        if (!isset($this->UserConfig["casesListRefreshTime"]) || (isset($this->UserConfig["casesListRefreshTime"]) && empty($this->UserConfig["casesListRefreshTime"]))) {
            $this->UserConfig["casesListRefreshTime"] = 120; //2 minutes
        }

        $this->UserConfig["TimeZone"] = date("T");

        return $this->UserConfig;
    }

    /**
     * setConfig
     *
     * @param string $route
     * @param object &$object
     * @param object &$to
     * @return void
     */
    public function setConfig($route, &$object, &$to)
    {
        if (!isset($to)) {
            $to = &$this->aConfig;
        }
        $routes = explode(',', $route);
        foreach ($routes as $r) {
            $ro = explode('/', $r);
            if (count($ro) > 1) {
                $rou = $ro;
                unset($rou[0]);
                if ($ro[0] === '*') {
                    foreach ($object as $k => $v) {
                        if (is_object($object)) {
                            if (!isset($to[$k])) {
                                $to[$k] = array();
                            }
                            $this->setConfig(implode('/', $rou), $object->{$k}, $to[$k]);
                        } else {
                            if (is_array($object)) {
                                if (!isset($to[$k])) {
                                    $to[$k] = array();
                                }
                                $this->setConfig(implode('/', $rou), $object[$k], $to[$k]);
                            }
                        }
                    }
                } else {
                    if (is_object($object)) {
                        if (!isset($to[$ro[0]])) {
                            $to[$ro[0]] = array();
                        }
                        $this->setConfig(implode('/', $rou), $object->{$ro[0]}, $to[$ro[0]]);
                    } else {
                        if (is_array($object)) {
                            if (!isset($to[$ro[0]])) {
                                $to[$ro[0]] = array();
                            }
                            $this->setConfig(implode('/', $rou), $object[$ro[0]], $to[$ro[0]]);
                        } else {
                            $to = $object;
                        }
                    }
                }
            } else {
                if ($ro[0] === '*') {
                    foreach ($object as $k => $v) {
                        if (is_object($object)) {
                            if (!isset($to[$k])) {
                                $to[$k] = array();
                            }
                            $to[$k] = $object->{$k};
                        } else {
                            if (is_array($object)) {
                                if (!isset($to[$k])) {
                                    $to[$k] = array();
                                }
                                $to[$k] = $object[$k];
                            }
                        }
                    }
                } else {
                    if (!isset($to[$r])) {
                        $to[$r] = array();
                    }
                    if (is_object($object)) {
                        $to[$r] = $object->{$r};
                    } elseif (is_array($object)) {
                        $to[$r] = $object[$r];
                    } else {
                        $to[$r] = $object;
                    }
                }
            }
        }
    }

    public function getDateFormats()
    {
        $arrayFormat = array();

        //The id, don't translate
        $arrayFormat[] = array("id" => "Y-m-d H:i:s", "name" => G::LoadTranslation("ID_DATE_FORMAT_1"));  //"Y-m-d H:i:s"     i.e: "2010-11-17 10:25:07"
        $arrayFormat[] = array("id" => "d/m/Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_2"));  //"d/m/Y"           i.e: "17/11/2010"
        $arrayFormat[] = array("id" => "m/d/Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_3"));  //"m/d/Y"           i.e: "11/17/2010"
        $arrayFormat[] = array("id" => "Y/d/m", "name" => G::LoadTranslation("ID_DATE_FORMAT_4"));  //"Y/d/m"           i.e: "2010/17/11"
        $arrayFormat[] = array("id" => "Y/m/d", "name" => G::LoadTranslation("ID_DATE_FORMAT_5"));  //"Y/m/d"           i.e: "2010/11/17"
        $arrayFormat[] = array("id" => "F j, Y, g:i a", "name" => G::LoadTranslation("ID_DATE_FORMAT_6"));  //"F j, Y, g:i a"   i.e: "November 17, 2010, 10:45 am"
        $arrayFormat[] = array("id" => "m.d.y", "name" => G::LoadTranslation("ID_DATE_FORMAT_7"));  //"m.d.y"           i.e: "11.17.10"
        $arrayFormat[] = array("id" => "j, n, Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_8"));  //"j, n, Y"         i.e: "17,11,2010"
        $arrayFormat[] = array("id" => "D M j G:i:s T Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_9"));  //"D M j G:i:s T Y" i.e: "Thu Nov 17 10:48:18 BOT 2010"
        $arrayFormat[] = array("id" => "D d M, Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_10")); //"D d M, Y"        i.e: "Thu 17 Nov, 2010"
        $arrayFormat[] = array("id" => "D M, Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_11")); //"D M, Y"          i.e: "Thu Nov, 2010"
        $arrayFormat[] = array("id" => "d M, Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_12")); //"d M, Y"          i.e: "17 Nov, 2010"
        $arrayFormat[] = array("id" => "d m, Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_13")); //"d m, Y"          i.e: "17 11, 2010"
        $arrayFormat[] = array("id" => "d.m.Y", "name" => G::LoadTranslation("ID_DATE_FORMAT_14")); //"d.m.Y"           i.e: "17.11.2010"

        return $arrayFormat;
    }

    public function getUserNameFormats()
    {
        $formats[] = Array('id' => '@firstName @lastName', //the id , don't translate
            'name' => G::loadTranslation('ID_USERNAME_FORMAT_1')  //label displayed, can be translated
        );
        $formats[] = Array('id' => '@firstName @lastName (@userName)', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_2'));
        $formats[] = Array('id' => '@userName', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_3'));
        $formats[] = Array('id' => '@userName (@firstName @lastName)', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_4'));
        $formats[] = Array('id' => '@lastName @firstName', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_5'));
        $formats[] = Array('id' => '@lastName, @firstName', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_6'));
        $formats[] = Array('id' => '@lastName, @firstName (@userName)', 'name' => G::loadTranslation('ID_USERNAME_FORMAT_7'));

        return $formats;
    }

    public function getSystemDate($dateTime)
    {
        $oConf = new Configurations();
        $oConf->loadConfig($obj, 'ENVIRONMENT_SETTINGS', '');
        $creationDateMask = isset($oConf->aConfig['dateFormat']) ? $oConf->aConfig['dateFormat'] : '';

        if ($creationDateMask != '') {
            if (strpos($dateTime, ' ') !== false) {
                list ($date, $time) = explode(' ', $dateTime);
                list ($y, $m, $d) = explode('-', $date);
                list ($h, $i, $s) = explode(':', $time);
                $dateTime = date($creationDateMask, mktime($h, $i, $s, $m, $d, $y));
            } else {
                list ($y, $m, $d) = explode('-', $dateTime);
                $dateTime = date($creationDateMask, mktime(0, 0, 0, $m, $d, $y));
            }
        }

        return $dateTime;
    }

    public function getEnvSetting($key = null, $data = null)
    {
        $this->loadConfig($obj, 'ENVIRONMENT_SETTINGS', '');

        if (isset($key)) {
            if (isset($this->aConfig[$key])) {
                if (isset($data) && is_array($data)) {
                    foreach ($data as $k => $v) {
                        $this->aConfig[$key] = str_replace('@' . $k, $v, $this->aConfig[$key]);
                    }
                }

                return $this->aConfig[$key];
            } else {
                return '';
            }
        } else {
            return $this->aConfig;
        }
    }

    /**
     * Gets the default fields and configuration for the cases list
     *
     * Return the fields and configuration
     *
     * @param string $action Action
     * @param string $translation Translation
     * @return array Return the fields and configuration
     *
     */
    public function casesListDefaultFieldsAndConfig($action, $translation = 1)
    {
        $caseColumns = array();
        $caseReaderFields = array();

        switch ($action) {
            case "draft":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "paused":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_THREAD_INDEX") : "**ID_THREAD_INDEX**", "dataIndex" => "APP_THREAD_INDEX", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DEL_INDEX") : "**ID_DEL_INDEX**", "dataIndex" => "DEL_INDEX", "width" => 80);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "APP_THREAD_INDEX");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "unassigned":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
                //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_COMPLETED_BY_USER") : "**ID_COMPLETED_BY_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 110);
                //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_FINISH_DATE") : "**ID_FINISH_DATE**", "dataIndex" => "APP_FINISH_DATE", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "sent":
            case "participated":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 120, "sortable" => true);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "search":
            case "simple_search":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 120 );
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 120, "sortable" => true);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DELEGATION_DATE") : "**ID_DELEGATION_DATE**", "dataIndex" => "DEL_DELEGATE_DATE", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 80);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_DELEGATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "to_revise":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => true);
                //$caseColumns[] = array("header" => "Sent By", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                //$caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "to_reassign":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => true);
                //$caseColumns[] = array("header" => ($translation == 1)? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

                $caseReaderFields[] = array("name" => "TAS_UID");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                //$caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "gral":
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => "PreUsrUid", "dataIndex" => "PREVIOUS_USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CURRENT_USER") : "**ID_CURRENT_USER**", "dataIndex" => "APP_CURRENT_USER", "width" => 90, "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_STATUS") : "**ID_STATUS**", "dataIndex" => "APP_STATUS", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
            case "todo":
            default:
                //todo
                $caseColumns[] = array("header" => "#", "dataIndex" => "APP_NUMBER", "width" => 45, "align" => "center");
                $caseColumns[] = array("header" => "UserUid", "dataIndex" => "USR_UID", "width" => 50, "hidden" => true, "hideable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SUMMARY") : "**ID_SUMMARY**", "dataIndex" => "CASE_SUMMARY", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASES_NOTES") : "**ID_CASES_NOTES**", "dataIndex" => "CASE_NOTES_COUNT", "width" => 45, "align" => "center", "sortable" => false);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_CASE") : "**ID_CASE**", "dataIndex" => "APP_TITLE", "width" => 150);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PROCESS") : "**ID_PROCESS**", "dataIndex" => "APP_PRO_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_TASK") : "**ID_TASK**", "dataIndex" => "APP_TAS_TITLE", "width" => 120);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_SENT_BY") : "**ID_SENT_BY**", "dataIndex" => "APP_DEL_PREVIOUS_USER", "width" => 90);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_DUE_DATE") : "**ID_DUE_DATE**", "dataIndex" => "DEL_TASK_DUE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_LAST_MODIFY") : "**ID_LAST_MODIFY**", "dataIndex" => "APP_UPDATE_DATE", "width" => 110);
                $caseColumns[] = array("header" => ($translation == 1) ? G::LoadTranslation("ID_PRIORITY") : "**ID_PRIORITY**", "dataIndex" => "DEL_PRIORITY", "width" => 50);

                $caseReaderFields[] = array("name" => "APP_UID");
                $caseReaderFields[] = array("name" => "USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_UID");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_FIRSTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_LASTNAME");
                $caseReaderFields[] = array("name" => "PREVIOUS_USR_USERNAME");
                $caseReaderFields[] = array("name" => "DEL_INDEX");
                $caseReaderFields[] = array("name" => "APP_NUMBER");
                $caseReaderFields[] = array("name" => "APP_TITLE");
                $caseReaderFields[] = array("name" => "APP_PRO_TITLE");
                $caseReaderFields[] = array("name" => "APP_TAS_TITLE");
                $caseReaderFields[] = array("name" => "APP_DEL_PREVIOUS_USER");
                $caseReaderFields[] = array("name" => "DEL_TASK_DUE_DATE");
                $caseReaderFields[] = array("name" => "APP_UPDATE_DATE");
                $caseReaderFields[] = array("name" => "DEL_PRIORITY");
                $caseReaderFields[] = array("name" => "APP_FINISH_DATE");
                $caseReaderFields[] = array("name" => "APP_CURRENT_USER");
                $caseReaderFields[] = array("name" => "APP_STATUS");
                $caseReaderFields[] = array("name" => "CASE_SUMMARY");
                $caseReaderFields[] = array("name" => "CASE_NOTES_COUNT");
                break;
        }

        return array("caseColumns" => $caseColumns, "caseReaderFields" => $caseReaderFields, "rowsperpage" => 20, "dateformat" => "M d, Y");
    }
}
 