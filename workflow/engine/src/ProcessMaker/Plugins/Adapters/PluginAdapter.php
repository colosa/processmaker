<?php

namespace ProcessMaker\Plugins\Adapters;

use G;
use PluginsRegistry;
use PMPluginRegistry;

/**
 * Adapts the plugin singleton file and converts it to table
 * Class PluginAdapter
 * @package ProcessMaker\Plugins\Adapters
 */
class PluginAdapter
{
    /**
     * This array is a map to change the old key to the new, corresponding of each class
     * @var array
     */
    private $keyNames = [
        'sNamespace' => 'Namespace',
        //MenuDetail
        'sMenuId' => 'MenuId',
        'sFilename' => 'Filename',
        //FolderDetail
        'sFolderId' => 'FolderId',
        'sFolderName' => 'FolderName',
        //TriggerDetail
        'sTriggerId' => 'TriggerId',
        'sTriggerName' => 'TriggerName',
        //RedirectDetail
        'sRoleCode' => 'RoleCode',
        'sPathMethod' => 'PathMethod',
        //StepDetail
        'sStepId' => 'StepId',
        'sStepName' => 'StepName',
        'sStepTitle' => 'StepTitle',
        'sSetupStepPage' => 'SetupStepPage',
        //CssFile->_aCSSStyleSheets
        'sCssFile' => 'CssFile',
        //ToolbarDetail->_aToolbarFiles
        'sToolbarId' => 'ToolbarId',
        //CaseSchedulerPlugin->_aCaseSchedulerPlugin
        'sActionId' => 'ActionId',
        'sActionForm' => 'ActionForm',
        'sActionSave' => 'ActionSave',
        'sActionExecute' => 'ActionExecute',
        'sActionGetFields' => 'ActionGetFields',
        //TaskExtendedProperty->_aTaskExtendedProperties
        //DashboardPage->_aDashboardPages
        'sPage' => 'Page',
        'sName' => 'Name',
        'sIcon' => 'Icon',
        //CronFile->_aCronFiles
        'namespace' => 'Namespace',
        'cronFile' => 'CronFile',
        //ImportCallBack->_aImportProcessCallbackFile
        //OpenReassignCallback->_aOpenReassignCallback
        'callBackFile' => 'CallBackFile',
        //JsFile->_aJavascripts
        'sCoreJsFile' => 'CoreJsFile',
        'pluginJsFile' => 'PluginJsFile',
    ];

    /**
     * Map the fields of the table with their type
     * @var array $attributes
     */
    private $attributes = [
        'sNamespace' => ['name' => 'PLUGIN_NAMESPACE', 'type' => 'string'],
        'sDescription' => ['name' => 'PLUGIN_DESCRIPTION', 'type' => 'string'],
        'sClassName' => ['name' => 'PLUGIN_CLASS_NAME', 'type' => 'string'],
        'sFriendlyName' => ['name' => 'PLUGIN_FRIENDLY_NAME', 'type' => 'string'],
        'sFilename' => ['name' => 'PLUGIN_FILE', 'type' => 'string'],
        'sPluginFolder' => ['name' => 'PLUGIN_FOLDER', 'type' => 'string'],
        'sSetupPage' => ['name' => 'PLUGIN_SETUP_PAGE', 'type' => 'string'],
        'aWorkspaces' => ['name' => 'PLUGIN_WORKSPACES', 'type' => 'array'],
        'sCompanyLogo' => ['name' => 'PLUGIN_COMPANY_LOGO', 'type' => 'string'],
        'iVersion' => ['name' => 'PLUGIN_VERSION', 'type' => 'int'],
        'enabled' => ['name' => 'PLUGIN_ENABLE', 'type' => 'bool'],
        'bPrivate' => ['name' => 'PLUGIN_PRIVATE', 'type' => 'bool'],
        '_aMenus' => ['name' => 'PLUGIN_MENUS', 'type' => 'array'],
        '_aFolders' => ['name' => 'PLUGIN_FOLDERS', 'type' => 'array'],
        '_aTriggers' => ['name' => 'PLUGIN_TRIGGERS', 'type' => 'array'],
        '_aPmFunctions' => ['name' => 'PLUGIN_PM_FUNCTIONS', 'type' => 'array'],
        '_aRedirectLogin' => ['name' => 'PLUGIN_REDIRECT_LOGIN', 'type' => 'array'],
        '_aSteps' => ['name' => 'PLUGIN_STEPS', 'type' => 'array'],
        '_aCSSStyleSheets' => ['name' => 'PLUGIN_CSS', 'type' => 'array'],
        '_aCss' => ['name' => 'PLUGIN_CSS', 'type' => 'array'],
        '_aJavascripts' => ['name' => 'PLUGIN_JS', 'type' => 'array'],
        '_aJs' => ['name' => 'PLUGIN_JS', 'type' => 'array'],
        '_restServices' => ['name' => 'PLUGIN_REST_SERVICE', 'type' => 'array'],
        '_aTaskExtendedProperties' => ['name' => 'PLUGIN_TASK_EXTENDED_PROPERTIES', 'type' => 'array'],
    ];

    /**
     * Data of the plugin singleton in array structure
     * @var array $PMPluginRegistry
     */
    private $PMPluginRegistry;

    /**
     * Returns the structure of the table in attributes
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Migrate the singleton plugin to tables
     * @param PMPluginRegistry $PMPluginsSingleton
     */
    public function migrate($PMPluginsSingleton)
    {
        $this->PMPluginRegistry = G::json_decode(G::json_encode($PMPluginsSingleton->getAttributes()), true);
        $this->parserNameKey();
        foreach ($this->PMPluginRegistry['_aPluginDetails'] as $nameSpace => $value) {
            $this->saveInTable($nameSpace, $this->PMPluginRegistry);
        }
    }

    /**
     * Change to new key property $keyNames
     * @return mixed
     */
    private function parserNameKey()
    {
        $aPluginDetails = $this->PMPluginRegistry['_aPluginDetails'];
        unset($this->PMPluginRegistry['_aPluginDetails']);
        $aPlugins = $this->PMPluginRegistry['_aPlugins'];
        unset($this->PMPluginRegistry['_aPlugins']);
        foreach ($this->PMPluginRegistry as $propertyKey => $propertyValue) {
            foreach ($propertyValue as $attKey => $attributes) {
                if (is_array($attributes)) {
                    foreach ($attributes as $index => $attribute) {
                        if (array_key_exists($index, $this->keyNames)) {
                            $newKey = $this->keyNames[$index];
                            $value = $this->PMPluginRegistry[$propertyKey][$attKey][$index];
                            $this->PMPluginRegistry[$propertyKey][$attKey][$newKey] = $value;
                            unset($this->PMPluginRegistry[$propertyKey][$attKey][$index]);
                        }
                    }
                }
            }
        }
        $this->PMPluginRegistry['_aPluginDetails'] = $aPluginDetails;
        $this->PMPluginRegistry['_aPlugins'] = $aPlugins;
        return $this->PMPluginRegistry;
    }

    /**
     * Save plugin in table PLUGINS_REGISTRY
     * @param string $Namespace Name of plugin
     * @param array $PMPluginRegistry
     */
    public function saveInTable($Namespace, $PMPluginRegistry)
    {
        $newStructurePlugin = $this->getAllAttributes($Namespace, $PMPluginRegistry);
        $plugin = $this->convertFieldTable($newStructurePlugin);
        if ($plugin['PLUGIN_NAMESPACE'] && $plugin['PLUGIN_CLASS_NAME'] && $plugin['PLUGIN_FILE']) {
            $fieldPlugin = PluginsRegistry::loadOrCreateIfNotExists(md5($plugin['PLUGIN_NAMESPACE']), $plugin);
            PluginsRegistry::update($fieldPlugin);
        }
    }

    /**
     * Extracts all attributes corresponding to a plugin
     * @param string $Namespace Name Plugin
     * @param array $PMPluginRegistry
     * @return array
     */
    private function getAllAttributes($Namespace, $PMPluginRegistry)
    {
        $PluginDetails = $PMPluginRegistry['_aPluginDetails'][$Namespace];
        unset($PMPluginRegistry['_aPluginDetails']);
        $Plugin = isset($PMPluginRegistry['_aPlugins'][$Namespace]) ? $PMPluginRegistry['_aPlugins'][$Namespace] : [];
        unset($PMPluginRegistry['_aPlugins']);
        $newStructurePlugin = array_merge($PluginDetails, $Plugin);
        foreach ($PMPluginRegistry as $propertyName => $propertyValue) {
            foreach ($propertyValue as $key => $plugin) {
                if (is_array($plugin) &&
                    (array_key_exists('Namespace', $plugin) && $plugin['Namespace'] == $Namespace)
                ) {
                    $newStructurePlugin[$propertyName][] = $plugin;
                } elseif (is_array($plugin) &&
                    array_key_exists('pluginName', $plugin) &&
                    $plugin['pluginName'] == $Namespace
                ) {
                    $newStructurePlugin[$propertyName][] = $plugin;
                } elseif (!is_int($key) && $key == $Namespace) {
                    $newStructurePlugin[$propertyName][$key] = $plugin;
                } elseif (is_string($plugin) && $plugin == $Namespace) {
                    $newStructurePlugin[$propertyName][] = $plugin;
                }
            }
        }
        return $newStructurePlugin;
    }

    /**
     * Convert de attributes to field of table PLUGINS_REGISTRY
     * @param array $plugin
     * @return array
     */
    private function convertFieldTable($plugin)
    {
        $fields = [];
        $extraAttributes = array_diff_key($plugin, $this->attributes);
        $fieldsInTable = array_intersect_key($plugin, $this->attributes);
        foreach ($this->attributes as $name => $property) {
            switch ($property['type']) {
                case 'string':
                    $valueField = array_key_exists($name, $fieldsInTable) ? $fieldsInTable[$name] : '';
                    break;
                case 'array':
                    $valueField = (array_key_exists($name, $fieldsInTable) && $fieldsInTable[$name]) ?
                        $fieldsInTable[$name] :
                        [];
                    $valueField = G::json_encode($valueField);
                    break;
                case 'int':
                    $valueField = array_key_exists($name, $fieldsInTable) ? $fieldsInTable[$name] : 0;
                    break;
                case 'bool':
                    $valueField = array_key_exists($name, $fieldsInTable) ?
                        ($fieldsInTable[$name] ? true : false) :
                        false;
                    break;
                default:
                    $valueField = array_key_exists($name, $fieldsInTable) ?
                        $fieldsInTable[$name] :
                        [];
                    break;
            }
            $fields[$property['name']] = $valueField;
        }
        $fields['PLUGIN_ATTRIBUTES'] = G::json_encode($extraAttributes);
        return $fields;
    }
}
