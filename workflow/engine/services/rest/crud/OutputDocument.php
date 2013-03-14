<?php

class Services_Rest_OutputDocument
{

    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $outDocUid Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($outDocUid = null)
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

                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_UID);
                $criteria->addSelectColumn(OutputDocumentPeer::PRO_UID);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_LANDSCAPE);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_MEDIA);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_LEFT_MARGIN);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_TOP_MARGIN);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_GENERATE);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_TYPE);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_CURRENT_REVISION);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_FIELD_MAPPING);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_VERSIONING);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_DESTINATION_PATH);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_TAGS);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD);
                $criteria->addSelectColumn(OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS);

                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = OutputDocumentPeer::retrieveByPK($outDocUid);
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
                    throw new RestException(417, "table OutputDocument ($paramValues)");
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

