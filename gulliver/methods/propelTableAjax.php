<?php
/**
 * propelTableAjax.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
  /* Includes */
	G::LoadSystem('pagedTable');
	G::LoadClass('propelTable');
	G::LoadInclude('ajax');
    G::LoadAllModelClasses();
    G::LoadAllPluginModelClasses();
    require_once ( 'classes/class.xmlfield_InputPM.php' );
	$id = get_ajax_value('ptID');
	$ntable= unserialize($_SESSION['pagedTable['.$id.']']);
	$page     = get_ajax_value('page');
	$function = get_ajax_value('function');

    //THIS BLOCK SET THE FILTER VARIABLES
    if (isset($ntable->filterForm_Id) && ($ntable->filterForm_Id!=='')) {

      $sPath = PATH_XMLFORM;
       //if the xmlform file doesn't exist, then try with the plugins folders
      if ( !is_file ( $sPath . G::getUIDName( $ntable->filterForm_Id ) ) ) {
        $aux = explode ( PATH_SEP, G::getUIDName( $ntable->filterForm_Id ) );
        //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
        if ( count($aux) == 2 && defined ( 'G_PLUGIN_CLASS' ) ) {
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $oPluginRegistry->isRegisteredFolder($aux[0]) ) {
            $sPath = PATH_PLUGINS;
          }
        }
      }

    $filterForm=new filterForm(G::getUIDName( $ntable->filterForm_Id ),$sPath);



    $filterForm->values=$_SESSION[$filterForm->id];
    parse_str( urldecode(get_ajax_value('filter')) , $newValues);
    if (isset($newValues['form'])) {
      $filterForm->setValues($newValues['form']);
      $filter = array();
      foreach($filterForm->fields as $fieldName => $field ){
        if (($field->dataCompareField!=='') && (isset($newValues['form'][$fieldName])))
          $filter[$field->dataCompareField] = $filterForm->values[$fieldName];
          $ntable->filterType[$field->dataCompareField] = $field->dataCompareType;
      }
      $ntable->filter = $filter;//G::http_build_query($filter);
    }
  }


  $fastSearch = get_ajax_value('fastSearch');
  if (isset($fastSearch)) {
    $ntable->fastSearch= htmlentities(urldecode($fastSearch), ENT_QUOTES, 'UTF-8');
    $page = 1;
  }

  //order by
  $orderBy = get_ajax_value('order');
   if (isset($orderBy)) {
       $orderBy = urldecode($orderBy);
       $ntable->orderBy = $orderBy;
   }

	if ( isset($page) && $page!=='' ) $ntable->currentPage = (int) $page;

	if ( function_exists('pagedTable_BeforeQuery'))
    pagedTable_BeforeQuery($ntable);

	//$ntable->prepareQuery();

	switch ($function)
	{
	case "showHideField":
	  $field=get_ajax_value('field');
	  $ntable->style[$field]['showInTable']=
	    ($ntable->style[$field]['showInTable']==='0')?'1':'0';
	  break;
	case "paint":
		break;
	case "delete":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlDelete,$field));
		break;
	case "update":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		parse_str(get_ajax_value('update'),$fieldup);
		foreach($fieldup as $key => $value) $field['new'.$key]=urldecode($value); //join
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlUpdate,$field));
		break;
	case "insert":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlInsert,$field));
		break;
	case "printForm":
		parse_str(get_ajax_value('field'),$field);
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->printForm(get_ajax_value('filename'),$field);
		return ;
	}

	$ntable->renderTable( 'content' );

  G::LoadClass('configuration');
  $conf = new Configurations();
  $conf->setConfig($ntable->__Configuration,$ntable,$conf->aConfig);
  $conf->saveConfig('pagedTable',$ntable->__OBJ_UID,'',$_SESSION['USER_LOGGED'],'');

?>
