<?php
/**
 * ProcessCategory.php
 * @package    workflow.engine.classes.model
 */

require_once 'classes/model/om/BaseProcessCategory.php';

/**
 * Skeleton subclass for representing a row from the 'PROCESS_CATEGORY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class ProcessCategory extends BaseProcessCategory
{
    public function getAll ($type = 'criteria')
    {
        $c = new Criteria('workflow');
        $c->addSelectColumn(ProcessCategoryPeer::CATEGORY_UID);
        $c->addSelectColumn(ProcessCategoryPeer::CATEGORY_NAME);
        $dataset = ProcessCategoryPeer::doSelectRS($c);
        $dataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );

        if ($type == 'array') {
            $result = Array();
            while ( $dataset->next() ) {
                $result[] = $dataset->getRow();
            }
            return $result;
        } else {
            return $c;
        }
    }

    public function loadByCategoryName($sCategoryName)
    {
        $c = new Criteria('workflow');
        $del = DBAdapter::getStringDelimiter();

        $c->clearSelectColumns();
        $c->addSelectColumn( ProcessCategoryPeer::CATEGORY_UID );
        $c->addSelectColumn( ProcessCategoryPeer::CATEGORY_NAME);

        $c->add(ProcessCategoryPeer::CATEGORY_NAME, $sCategoryName);
        $dataset = ProcessCategoryPeer::doSelectRS($c);
        $dataset->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
        $dataset->next();
        $aRow = $dataset->getRow();
        return $aRow;
    }
}

