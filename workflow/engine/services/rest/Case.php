<?php

class Services_Rest_Case
{
    protected function get($id = '', $start=null, $limit=null, $type=null, $filter=null, $search=null, $process=null, $user=null, $status=null, $typeResource=null, $dateFrom=null, $dateTo=null)
    {
        if (empty($id)) {
            // getting all records.
            G::loadClass('applications');
            $app = new Applications();
            $userUid = Services_Rest_Auth::$userId;

            return $app->getAll($userUid, $start, $limit, $type, $filter, $search, $process, $status, $typeResource, $dateFrom, $dateTo);
        } else {
            // get a specific record.
            G::loadClass('wsBase');
            $wsBase = new wsBase();
            return $wsBase->getCaseInfo($id);
        }
    }
}
