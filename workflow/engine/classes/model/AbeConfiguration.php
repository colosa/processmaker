<?php
require_once 'classes/model/om/BaseAbeConfiguration.php';


/**
 * Skeleton subclass for representing a row from the 'ABE_CONFIGURATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AbeConfiguration extends BaseAbeConfiguration
{

    private $filterThisFields = array('ABE_UID', 'PRO_UID', 'TAS_UID', 'ABE_TYPE',
                                      'ABE_TEMPLATE', 'ABE_DYN_TYPE', 'DYN_UID','ABE_EMAIL_FIELD',
                                      'ABE_ACTION_FIELD', 'ABE_CASE_NOTE_IN_RESPONSE', 'ABE_CREATE_DATE','ABE_UPDATE_DATE','ABE_MAILSERVER_OR_MAILCURRENT','ABE_SUBJECT_FIELD');

    public function load($abeUid)
    {
        try {
            $abeConfigurationInstance = AbeConfigurationPeer::retrieveByPK($abeUid);
            $fields = $abeConfigurationInstance->toArray(BasePeer::TYPE_FIELDNAME);

            return $fields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function createOrUpdate($data)
    {
        foreach ($data as $field => $value) {
            if (!in_array($field, $this->filterThisFields)) {
                unset($data[$field]);
            }
        }

        $connection = Propel::getConnection(AbeConfigurationPeer::DATABASE_NAME);

        try {
            if (!isset($data['ABE_UID'])) {
                $data['ABE_UID'] = '';
            }

            if ($data['ABE_UID'] == '') {
                $data['ABE_UID'] = G::generateUniqueID();
                $data['ABE_CREATE_DATE'] = date('Y-m-d H:i:s');
                $abeConfigurationInstance = new AbeConfiguration();
            } else {
                $abeConfigurationInstance = AbeConfigurationPeer::retrieveByPK($data['ABE_UID']);
            }

            $data['ABE_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $abeConfigurationInstance->fromArray($data, BasePeer::TYPE_FIELDNAME);

            if ($abeConfigurationInstance->validate()) {
                $connection->begin();
                $result = $abeConfigurationInstance->save();
                $connection->commit();

                return $data['ABE_UID'];
            } else {
                $message = '';
                $validationFailures = $abeConfigurationInstance->getValidationFailures();

                foreach ($validationFailures as $validationFailure) {
                    $message .= $validationFailure->getMessage() . '. ';
                }

                throw (new Exception('Error trying to update: ' . $message));
            }
        } catch (Exception $error) {
            $connection->rollback();

            throw $error;
        }
    }

    public function deleteByTasUid($tasUid)
    {
        try {
            $criteria = new Criteria('workflow');
            $criteria->add(AbeConfigurationPeer::TAS_UID, $tasUid);
            AbeConfigurationPeer::doDelete($criteria);
        } catch (Exception $error) {
            throw $error;
        }
    }
}

// AbeConfiguration

