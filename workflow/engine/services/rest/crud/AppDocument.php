<?php

class Services_Rest_AppDocument
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $appDocUid, $docVersion Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($appDocUid=null, $docVersion=null)
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

                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
                $criteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
                $criteria->addSelectColumn(AppDocumentPeer::APP_UID);
                $criteria->addSelectColumn(AppDocumentPeer::DEL_INDEX);
                $criteria->addSelectColumn(AppDocumentPeer::DOC_UID);
                $criteria->addSelectColumn(AppDocumentPeer::USR_UID);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_INDEX);
                $criteria->addSelectColumn(AppDocumentPeer::FOLDER_UID);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_PLUGIN);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_TAGS);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_STATUS);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_STATUS_DATE);
                $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_FIELDNAME);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = AppDocumentPeer::retrieveByPK($appDocUid, $docVersion);
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
                    throw new RestException(417, "table AppDocument ($paramValues)" );
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
