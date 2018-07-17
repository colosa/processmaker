<?php

require_once 'classes/model/om/BasePluginsRegistry.php';


/**
 * Skeleton subclass for representing a row from the 'PLUGINS_REGISTRY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class PluginsRegistry extends BasePluginsRegistry
{
    /**
     * Load all Plugins
     * @param string $keyType
     * @return array
     * @throws \Exception
     */
    public static function loadPlugins($keyType = BasePeer::TYPE_FIELDNAME)
    {
        $criteria = new Criteria();
        $dataSet = PluginsRegistryPeer::doSelect($criteria);
        $plugins = [];
        /** @var PluginsRegistry $row */
        foreach ($dataSet as $row) {
            $plugins[] = $row->toArray($keyType);
        }
        return $plugins;
    }

    /**
     * Get all Plugins Enabled
     * @param string $keyType
     * @return array
     */
    public static function getPluginsEnabled($keyType = BasePeer::TYPE_FIELDNAME)
    {
        $criteria = new Criteria();
        $criteria->add(PluginsRegistryPeer::PLUGIN_ENABLE, true);
        $dataSet = PluginsRegistryPeer::doSelect($criteria);
        $plugins = [];
        /** @var PluginsRegistry $row */
        foreach ($dataSet as $row) {
            $plugins[] = $row->toArray($keyType);
        }
        return $plugins;
    }

    /**
     * Load plugin with Uid
     * @param string $prUid
     * @return array
     * @throws Exception
     */
    public static function load($prUid)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK($prUid);
        if ($oPluginsRegistry) {
            /** @var array $aFields */
            $aFields = $oPluginsRegistry->toArray(BasePeer::TYPE_FIELDNAME);
            return $aFields;
        } else {
            throw new Exception("Plugin does not exist!");
        }
    }

    /**
     * Check if there is a plugin uid
     * @param $prUid
     * @return mixed|bool
     */
    public static function exists($prUid)
    {
        $oPluginsRegistry = PluginsRegistryPeer::retrieveByPk($prUid);
        if ($oPluginsRegistry) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Load or creates if the record does not exist
     * Load and makes a union with the data sent
     *
     * @param string $prUid
     * @param array $pluginData
     * @return array
     */
    public static function loadOrCreateIfNotExists($prUid, $pluginData = [])
    {
        if (!self::exists($prUid)) {
            $pluginData['PR_UID'] = $prUid;
            self::create($pluginData);
        } else {
            $fields = self::load($prUid);
            $pluginData = array_merge($fields, $pluginData);
        }
        return $pluginData;
    }

    /**
     * Creates a record in the PLUGINS_REGISTRY table
     * @param array $aData
     * @return bool
     * @throws Exception
     */
    public static function create($aData)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = new PluginsRegistry();
            $oPluginsRegistry->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($oPluginsRegistry->validate()) {
                $oConnection->begin();
                $oPluginsRegistry->save();
                $oConnection->commit();
                return true;
            } else {
                $sMessage = '';
                $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                /** @var ValidationFailed $oValidationFailure */
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw (new Exception('The registry cannot be created!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Modifies a record in the PLUGINS_REGISTRY table
     * @param array $aData
     * @return int
     * @throws Exception
     */
    public static function update($aData)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK($aData['PR_UID']);
            if ($oPluginsRegistry) {
                $oPluginsRegistry->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oPluginsRegistry->validate()) {
                    $oConnection->begin();
                    $iResult = $oPluginsRegistry->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                    /** @var ValidationFailed $oValidationFailure */
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception('The registry cannot be updated!<br />' . $sMessage));
                }
            } else {
                throw (new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Activate a plugin with your name
     * @param string $Namespace
     * @return int
     * @throws Exception
     */
    public static function enable($Namespace)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK(md5($Namespace));
            if ($oPluginsRegistry) {
                $oPluginsRegistry->fromArray(['PLUGIN_ENABLE' => true], BasePeer::TYPE_FIELDNAME);
                if ($oPluginsRegistry->validate()) {
                    $oConnection->begin();
                    $iResult = $oPluginsRegistry->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                    /** @var ValidationFailed $oValidationFailure */
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception('The registry cannot be updated!<br />' . $sMessage));
                }
            } else {
                throw (new Exception('This Plugin doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Disable a plugin with your name
     * @param string $Namespace
     * @return int
     * @throws Exception
     */
    public static function disable($Namespace)
    {
        $oConnection = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        try {
            $oPluginsRegistry = PluginsRegistryPeer::retrieveByPK(md5($Namespace));
            if ($oPluginsRegistry) {
                $oPluginsRegistry->fromArray(['PLUGIN_ENABLE' => false], BasePeer::TYPE_FIELDNAME);
                if ($oPluginsRegistry->validate()) {
                    $oConnection->begin();
                    $iResult = $oPluginsRegistry->save();
                    $oConnection->commit();
                    return $iResult;
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oPluginsRegistry->getValidationFailures();
                    /** @var ValidationFailed $oValidationFailure */
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw (new Exception('The registry cannot be updated!<br />' . $sMessage));
                }
            } else {
                throw (new Exception('This Plugin doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }
}
