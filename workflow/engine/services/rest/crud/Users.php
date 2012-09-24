<?php

class Services_Rest_Users
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $usrUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($usrUid=null)
    {
        $result = array();
        try {
            $noArguments = true;
            $argumentList = func_get_args();
            foreach ($argumentList as $arg) {
                if (!is_null($arg)) {
                    $noArguments = false;
                }
            }

            if ($noArguments) {
                $criteria = new Criteria('workflow');

                $criteria->addSelectColumn(UsersPeer::USR_UID);
                $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
                $criteria->addSelectColumn(UsersPeer::USR_PASSWORD);
                $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
                $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
                $criteria->addSelectColumn(UsersPeer::USR_DUE_DATE);
                $criteria->addSelectColumn(UsersPeer::USR_CREATE_DATE);
                $criteria->addSelectColumn(UsersPeer::USR_UPDATE_DATE);
                $criteria->addSelectColumn(UsersPeer::USR_STATUS);
                $criteria->addSelectColumn(UsersPeer::USR_COUNTRY);
                $criteria->addSelectColumn(UsersPeer::USR_CITY);
                $criteria->addSelectColumn(UsersPeer::USR_LOCATION);
                $criteria->addSelectColumn(UsersPeer::USR_ADDRESS);
                $criteria->addSelectColumn(UsersPeer::USR_PHONE);
                $criteria->addSelectColumn(UsersPeer::USR_FAX);
                $criteria->addSelectColumn(UsersPeer::USR_CELLULAR);
                $criteria->addSelectColumn(UsersPeer::USR_ZIP_CODE);
                $criteria->addSelectColumn(UsersPeer::DEP_UID);
                $criteria->addSelectColumn(UsersPeer::USR_POSITION);
                $criteria->addSelectColumn(UsersPeer::USR_RESUME);
                $criteria->addSelectColumn(UsersPeer::USR_BIRTHDAY);
                $criteria->addSelectColumn(UsersPeer::USR_ROLE);
                $criteria->addSelectColumn(UsersPeer::USR_REPORTS_TO);
                $criteria->addSelectColumn(UsersPeer::USR_REPLACED_BY);
                $criteria->addSelectColumn(UsersPeer::USR_UX);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = UsersPeer::retrieveByPK($usrUid);
                if ($record) {
                    $result = $record->toArray(BasePeer::TYPE_FIELDNAME);
                } else {
                    $paramValues = "";
                    foreach ($argumentList as $arg) {
                        $paramValues .= (strlen($paramValues) ) ? ', ' : '';
                        if (!is_null($arg)) {
                            $paramValues .= "$arg";
                        } else {
                            $paramValues .= "NULL";
                        }
                    }
                    throw new RestException(417, "table Users ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }


}
