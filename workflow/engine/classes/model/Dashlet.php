<?php

require_once 'classes/model/om/BaseDashlet.php';


/**
 * Skeleton subclass for representing a row from the 'DASHLET' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Dashlet extends BaseDashlet
{
    public function load($dasUid)
    {
        try {
            $dashlet = DashletPeer::retrieveByPK($dasUid);
            if (!is_null($dashlet)) {
                return $dashlet->toArray(BasePeer::TYPE_FIELDNAME);
            } else {
                return null;
            }
        } catch (Exception $error) {
            throw $error;
        }
    }
}

