<?php

class Services_Rest_InputDocument
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $inpDocUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($inpDocUid=null)
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

                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_UID);
                $criteria->addSelectColumn(InputDocumentPeer::PRO_UID);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_FORM_NEEDED);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_ORIGINAL);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_PUBLISHED);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_VERSIONING);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_DESTINATION_PATH);
                $criteria->addSelectColumn(InputDocumentPeer::INP_DOC_TAGS);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = InputDocumentPeer::retrieveByPK($inpDocUid);
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
                    throw new RestException(417, "table InputDocument ($paramValues)" );
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
