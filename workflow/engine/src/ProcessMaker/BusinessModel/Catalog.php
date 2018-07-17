<?php
namespace ProcessMaker\BusinessModel;

use \G;

/**
 * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Catalog
{

    /**
     * Get CatalogUid by UserUid
     *
     * @param string $cat_type type of catalog
     *
     * return uid
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     **/
    public function getCatalogByType($cat_type)
    {
        $catalog = new \Catalog();
        $response = $catalog->loadByType($cat_type);
        return $response;
    }

    /**
     * Create Catalog
     *
     * @param array $arrayData Data
     *
     * return array Return data of the new Group created
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function create($arrayData)
    {
        $catalog = new \Catalog();
        $response = $catalog->create($arrayData);
        return $response;
    }

    /**
     * Update Catalog.
     *
     * @param string $cat_uid    Unique id of Group
     * @param string $cat_type   Unique id of Group
     * @param array  $arrayData  Data
     *
     * @return array Return data of the new Group update
     *
     * @access private
     * @deprecated since 3.2.2
     */
    public function update($cat_uid, $cat_type, $arrayData)
    {
        $catalog = new \Catalog();
        $arrayData['CAT_UID'] = $cat_uid;
        $arrayData['CAT_TYPE'] = !isset($arrayData['CAT_TYPE'])
            ? $cat_type
            : $arrayData['CAT_TYPE'];
        $response = $catalog->createOrUpdate($arrayData);
        return $response;
    }

    /**
     * Delete Catalog
     *
     * @param string $cat_uid   Unique id of Group
     * @param string $cat_type  Unique id of Group
     *
     * return void
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function delete($cat_uid, $cat_type)
    {
        $catalog = new \Catalog();
        $response = $catalog->delete($cat_uid, $cat_type);
        return $response;
    }
}

