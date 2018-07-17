<?php

namespace ProcessMaker\Plugins\Traits;

use BasePeer;
use G;
use PluginsRegistry;
use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\FolderDetail;
use ProcessMaker\Plugins\Interfaces\JsFile;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\RedirectDetail;
use ProcessMaker\Plugins\Interfaces\StepDetail;
use ProcessMaker\Plugins\Interfaces\TriggerDetail;

/**
 * Trait PluginStructure
 * @package ProcessMaker\Plugins\Traits
 */
trait PluginStructure
{
    /** @var array Plugins Details */
    private $_aPluginDetails = array();
    /** @var array Class structure of plugin */
    private $_aPlugins = array();
    /** @var array Menus added */
    private $_aMenus = array();
    /** @var array Folders added */
    private $_aFolders = array();
    /** @var array Triggers added */
    private $_aTriggers = array();
    /** @var array Functions added */
    private $_aPmFunctions = array();
    /** @var array Redirect login added */
    private $_aRedirectLogin = array();
    /** @var array Steps added */
    private $_aSteps = array();
    /** @var array Css added */
    private $_aCss = array();
    /** @var array Registry a plugin javascript to include with js core */
    private $_aJs = array();
    /** @var array Contains all rest services classes */
    private $_restServices = array();
    /** @var array Reports added */
    private $_aReports = array();
    /** @var array */
    private $_aDashboardPages = array();
    /** @var array Dashlets added */
    private $_aDashlets = array();
    /** @var array Toolbar file added */
    private $_aToolbarFiles = array();
    /** @var array Case Scheduler added */
    private $_aCaseSchedulerPlugin = array();
    /** @var array Task Extended added */
    private $_aTaskExtendedProperties = array();
    /** @var array Cron files added */
    private $_aCronFiles = array();
    /** @var array Designer menu added */
    private $_arrayDesignerMenu = array();
    /** @var array Menu options added */
    private $_aMenuOptionsToReplace = array();
    /** @var array Import process callback added */
    private $_aImportProcessCallbackFile = array();
    /** @var array Open reassign Callback added */
    private $_aOpenReassignCallback = array();
    /** @var array Designer Source added */
    private $_arrayDesignerSourcePath = array();
    /** @var array Extend Services added */
    private $_restExtendServices = array();
    /** @var array Service enabled added */
    private $_restServiceEnabled = array();

    /**
     * Get information of all Plugins
     * @return array
     */
    public function getAllPluginsDetails()
    {
        return $this->_aPluginDetails;
    }

    /**
     * Get information of all Plugins
     * @return array
     */
    public function getPlugins()
    {
        return $this->_aPlugins;
    }

    /**
     * Ser information of all Plugins
     * @param array $plugins
     */
    public function setPlugins($plugins)
    {
        $this->_aPlugins = $plugins;
    }

    /**
     * Builds the Plugin structure based on its classes
     */
    private function constructStructure()
    {
        $Plugins = PluginsRegistry::loadPlugins(BasePeer::TYPE_PHPNAME);
        foreach ($Plugins as $plugin) {
            $this->_aPluginDetails[$plugin['PluginNamespace']] = $this->buildPluginDetails($plugin);
            if ($plugin['PluginEnable']) {
                $this->buildMenus(G::json_decode($plugin['PluginMenus'], true));
                $this->buildFolders(G::json_decode($plugin['PluginFolders'], true));
                $this->buildTriggers(G::json_decode($plugin['PluginTriggers'], true));
                $this->buildPmFunctions(G::json_decode($plugin['PluginPmFunctions'], true));
                $this->buildRedirectLogin(G::json_decode($plugin['PluginRedirectLogin'], true));
                $this->buildSteps(G::json_decode($plugin['PluginSteps'], true));
                $this->buildCss(G::json_decode($plugin['PluginCss'], true));
                $this->buildJs(G::json_decode($plugin['PluginJs'], true));
                $this->buildRestService(G::json_decode($plugin['PluginRestService'], true));
                $this->buildAttributes($plugin['PluginNamespace'], G::json_decode($plugin['PluginAttributes']));
            }
        }
    }

    /**
     * Build the class PluginDetail
     * @param array $plugin Information of plugin
     * @return PluginDetail
     */
    private function buildPluginDetails($plugin)
    {
        return new PluginDetail(
            $plugin['PluginNamespace'],
            $plugin['PluginClassName'],
            $plugin['PluginFile'],
            $plugin['PluginFriendlyName'],
            $plugin['PluginFolder'],
            $plugin['PluginDescription'],
            $plugin['PluginSetupPage'],
            $plugin['PluginVersion'],
            $plugin['PluginCompanyLogo'],
            G::json_decode($plugin['PluginWorkspaces']),
            $plugin['PluginEnable'],
            $plugin['PluginPrivate']
        );
    }

    /**
     * Build the class MenuDetail
     * @param array $menus
     */
    private function buildMenus($menus)
    {
        $response = [];
        foreach ($menus as $menu) {
            $response[] = new MenuDetail($menu['Namespace'], $menu['MenuId'], $menu['Filename']);
        }
        $this->_aMenus = array_merge($this->_aMenus, $response);
    }

    /**
     * Build the class FolderDetail
     * @param array $folders
     */
    private function buildFolders($folders)
    {
        $response = [];
        foreach ($folders as $folder) {
            $response[] = new FolderDetail($folder['Namespace'], $folder['FolderId'], $folder['FolderName']);
        }
        $this->_aFolders = array_merge($this->_aFolders, $response);
    }

    /**
     * Build the class TriggerDetail
     * @param array $triggers
     */
    private function buildTriggers($triggers)
    {
        $response = [];
        foreach ($triggers as $trigger) {
            $response[] = new TriggerDetail($trigger['Namespace'], $trigger['TriggerId'], $trigger['TriggerName']);
        }
        $this->_aTriggers = array_merge($this->_aTriggers, $response);
    }

    /**
     * Build the array
     * @param array $pmFunctions
     */
    private function buildPmFunctions($pmFunctions)
    {
        foreach ($pmFunctions as $pmFunction) {
            $this->_aPmFunctions = array_merge($this->_aPmFunctions, [$pmFunction]);
        }
    }

    /**
     * Build the class RedirectDetail
     * @param array $redirectLogin
     */
    private function buildRedirectLogin($redirectLogin)
    {
        $response = [];
        foreach ($redirectLogin as $trigger) {
            $response[] = new RedirectDetail($trigger['Namespace'], $trigger['RoleCode'], $trigger['PathMethod']);
        }
        $this->_aRedirectLogin = array_merge($this->_aRedirectLogin, $response);
    }

    /**
     * Build the class StepDetail
     * @param array $steps
     */
    private function buildSteps($steps)
    {
        $response = [];
        foreach ($steps as $step) {
            $response[] = new StepDetail(
                $step['Namespace'],
                $step['StepId'],
                $step['StepName'],
                $step['StepTitle'],
                $step['SetupStepPage']
            );
        }
        $this->_aSteps = array_merge($this->_aSteps, $response);
    }

    /**
     * Build the class CssFile
     * @param array $css
     */
    private function buildCss($css)
    {
        $response = [];
        foreach ($css as $c) {
            $response[] = new CssFile($c['Namespace'], $c['CssFile']);
        }
        $this->_aCss = array_merge($this->_aCss, $response);
    }

    /**
     * Build the class JsFile
     * @param array $js
     */
    private function buildJs($js)
    {
        $response = [];
        foreach ($js as $j) {
            $response[] = new JsFile($j['Namespace'], $j['CoreJsFile'], $j['PluginJsFile']);
        }
        $this->_aJs = array_merge($this->_aJs, $response);
    }

    /**
     * Build the array
     * @param array $restServices
     */
    private function buildRestService($restServices)
    {
        $this->_restServices = array_merge($this->_restServices, $restServices);
    }

    /**
     * Build other properties that are not considered in the schema of the table
     * @param string $namespace
     * @param array $attributes
     */
    private function buildAttributes($namespace, $attributes)
    {
        $this->_aPlugins[$namespace] = (object)$this->_aPluginDetails[$namespace]->getAttributes();
        foreach ($attributes as $key => $value) {
            $this->_aPlugins[$namespace]->{$key} = $value;
            if (property_exists($this, $key)) {
                $this->{$key} = array_merge($this->{$key}, (array)$value);
            }
        }
    }

    /**
     * Get all attributes of a plugin
     * @param string $Namespace
     * @return array
     */
    private function getAllAttributes($Namespace)
    {
        $PluginRegistry = clone $this;
        /** @var PluginDetail $PluginDetails */
        $PluginDetails = $PluginRegistry->_aPluginDetails[$Namespace];
        unset($PluginRegistry->_aPluginDetails);
        $Plugin = isset($PluginRegistry->_aPlugins[$Namespace]) ?
            G::json_decode(G::json_encode($PluginRegistry->_aPlugins[$Namespace]), true) :
            [];
        unset($PluginRegistry->_aPlugins);
        $newStructurePlugin = array_merge($Plugin, $PluginDetails->getAttributes());
        foreach ($PluginRegistry as $propertyName => $propertyValue) {
            foreach ($propertyValue as $key => $plugin) {
                if (is_object($plugin) &&
                    property_exists($plugin, 'Namespace') && $plugin->equalNamespaceTo($Namespace)
                ) {
                    $newStructurePlugin[$propertyName][] = $plugin;
                } elseif (is_object($plugin) &&
                    property_exists($plugin, 'pluginName') &&
                    $plugin->pluginName == $Namespace
                ) {
                    $newStructurePlugin[$propertyName][] = $plugin;
                } elseif (is_string($key) && $key == $Namespace) {
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
        $extraAttributes = array_diff_key($plugin, $this->adapter->getAttributes());
        $fieldsInTable = array_intersect_key($plugin, $this->adapter->getAttributes());
        foreach ($this->adapter->getAttributes() as $name => $property) {
            switch ($property['type']) {
                case 'string':
                    $valueField = array_key_exists($name, $fieldsInTable) ? $fieldsInTable[$name] : '';
                    break;
                case 'array':
                    $valueField = [];
                    if ((array_key_exists($name, $fieldsInTable) && $fieldsInTable[$name])) {
                        foreach ($fieldsInTable[$name] as $index => $item) {
                            if (method_exists($item, 'getAttributes')) {
                                $valueField[$index] = $item->getAttributes();
                            } else {
                                $valueField[$index] = $item;
                            }
                        }
                    }
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
