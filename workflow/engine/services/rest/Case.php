<?php

class Services_Rest_Case
{
    public function get()
    {
        echo 'hello world';
    }

    public function options22()
    {
        echo 'opts';
    }

    public function post()
    {
        header('Content-Type: application/json');
        echo '{"response": "hello post"}';
    }

    protected function get11($id = '', $start=null, $limit=null, $type=null, $filter=null, $search=null, $process=null, $user=null, $status=null, $typeResource=null, $dateFrom=null, $dateTo=null)
    {
        if (empty($id)) {
            // getting all records.
            G::loadClass('applications');
            $app = new Applications();
            $userUid = Services_Rest_Auth::$userId;

            return $app->getAll($userUid, $start, $limit, $type, $filter, $search, $process, $user, $status, $typeResource, $dateFrom, $dateTo);
        } else {
            // get a specific record.
            G::loadClass('wsBase');
            $wsBase = new wsBase();
            return $wsBase->getCaseInfo($id);
        }
    }
}
