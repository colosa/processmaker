<?php

namespace ProcessMaker\BusinessModel;

/**
 * Description of ActionsByEmailService
 * 
 */
class ActionsByEmail
{

    public function saveConfiguration($params)
    {
        if (\PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
            $feature = $params['ActionsByEmail'];
            switch ($feature['type']) {
                case 'configuration':
                    require_once 'classes/model/AbeConfiguration.php';
                    $abeConfigurationInstance = new \AbeConfiguration();
                    if(isset($feature['fields']['ABE_CASE_NOTE_IN_RESPONSE'])){
                        $noteValues = json_decode($feature['fields']['ABE_CASE_NOTE_IN_RESPONSE']);
                        foreach ($noteValues as $value) {
                            $feature['fields']['ABE_CASE_NOTE_IN_RESPONSE'] = $value;
                        }
                    }
                    $abeConfigurationInstance->createOrUpdate($feature['fields']);
                    break;
                default:
                    break;
            }
        }
    }

    public function loadConfiguration($params)
    {
        if ($params['type'] != 'activity' 
            || !\PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0='))
        {
            return false;
        }
        require_once 'classes/model/AbeConfiguration.php';

        $criteria = new \Criteria();
        $criteria->add(\AbeConfigurationPeer::PRO_UID, $params['PRO_UID']);
        $criteria->add(\AbeConfigurationPeer::TAS_UID, $params['TAS_UID']);
        $result = \AbeConfigurationPeer::doSelectRS($criteria);
        $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $configuration = array();
        if ($configuration = $result->getRow()) {
            $configuration['ABE_UID'] = $configuration['ABE_UID'];
            $configuration['ABE_TYPE'] = $configuration['ABE_TYPE'];
            $configuration['DYN_UID'] = $configuration['DYN_UID'];
            $configuration['ABE_TEMPLATE'] = $configuration['ABE_TEMPLATE'];
            $configuration['ABE_SUBJECT_FIELD'] = $configuration['ABE_SUBJECT_FIELD'];
            $configuration['ABE_EMAIL_FIELD'] = $configuration['ABE_EMAIL_FIELD'];
            $configuration['ABE_ACTION_FIELD'] = $configuration['ABE_ACTION_FIELD'];
            $configuration['ABE_MAILSERVER_OR_MAILCURRENT'] = $configuration['ABE_MAILSERVER_OR_MAILCURRENT'];
            $configuration['ABE_CASE_NOTE_IN_RESPONSE'] = $configuration['ABE_CASE_NOTE_IN_RESPONSE'] ? '["1"]' : '[]';
        }
        $configuration['feature'] = 'ActionsByEmail';
        $configuration['prefix'] = 'abe';
        $configuration['PRO_UID'] = $params['PRO_UID'];
        $configuration['TAS_UID'] = $params['TAS_UID'];
        $configuration['SYS_LANG'] = SYS_LANG;
        return $configuration;
    }

}
