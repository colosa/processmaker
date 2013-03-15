<?php

/**
 * tools/ajaxListener.php Ajax Listener for Cases rpc requests
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
/**
 *
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @date Jan 10th, 2010
 */
require "classes/model/Translation.php";

$action = $_REQUEST['action'];
unset($_REQUEST['action']);
$ajax = new Ajax();
$ajax->$action($_REQUEST);

class Ajax
{

    public function getList($params)
    {
        $search = isset($params['search']) ? $params['search'] : null;
        $params['dateFrom'] = str_replace('T00:00:00', '', $params['dateFrom']);
        $params['dateTo'] = str_replace('T00:00:00', '', $params['dateTo']);
        $result = Translation::getAll('en', $params['start'], $params['limit'], $search, $params['dateFrom'], $params['dateTo']);
        //$result = Translation::getAll('en', $params['start'], $params['limit'], $search);


        /* foreach($result->data as $i=>$row){
          $result->data[$i]['TRN_VALUE'] = substr($row['TRN_VALUE'], 0, 15) . '...';
          } */

        echo G::json_encode($result);
    }

    public function save()
    {
        try {
            require_once ("classes/model/Translation.php");
            $id = $_POST['id'];
            $label = preg_replace("[\n|\r|\n\r]", ' ', $_POST['label']);

            $res = Translation::addTranslation('LABEL', $id, 'en', $label);
            if ($res['codError'] < 0) {
                $result->success = false;
                $result->msg = $res['message'];
            } else {
                $result->success = true;
                $result->msg = 'Label ' . $id . ' saved Successfully!';
            }
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode($result);
    }

    public function delete()
    {
        require_once ("classes/model/Translation.php");
        $ids = explode(',', $_POST['IDS']);
        $category = 'LABEL';

        try {
            foreach ($ids as $id) {
                $tr = TranslationPeer::retrieveByPK($category, $id, 'en');
                if ((is_object($tr) && get_class($tr) == 'Translation')) {
                    $tr->delete();
                }
            }

            $result->success = true;
            $result->msg = 'Deleted Successfully!';
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode($result);
    }

    public function rebuild()
    {
        try {
            require_once ("classes/model/Translation.php");
            $t = new Translation();
            $result = Translation::generateFileTranslation('en');
            $result['success'] = true;
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        print G::json_encode($result);
    }
}

