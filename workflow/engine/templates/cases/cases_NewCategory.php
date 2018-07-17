<?php

   $oCase = new Cases();
   $_DBArray['NewCase'] = $oCase->getStartCasesPerType( $_SESSION['USER_LOGGED'], $_GET['change'] );
   
    
   $uplogo = PATH_TPL . 'cases' . PATH_SEP . 'cases_NewCategory.html' ;
   $template = new TemplatePower( $uplogo );
   $template->prepare();
   $template->assign ('CHANGE_LINK', G::LoadTranslation('ID_CHANGE_VIEW'));
   $template->assign ('START_NEW_CASE', G::LoadTranslation('ID_START_NEW_CASE'));
   
   $newArrayPerCategory=array();
   $i=1;                        
   //we put in order per category
   while($i <sizeof($_DBArray['NewCase'] )){ 
    $newArrayPerCategory[$_DBArray['NewCase'][$i]['catname']][]=array('uid'=>$_DBArray['NewCase'][$i]['uid'], 'value'=> $_DBArray['NewCase'][$i]['value'] );
    $i++;
   }
   //we show the categories names and wich element
   foreach($newArrayPerCategory as $kk => $vv){
   	$template->newBlock( 'CATEGORY_NAME');
   	$template->assign ('CATEGORY_NAME', $kk);
   	foreach($vv as $k => $v) {
   		$template->newBlock( 'CATEGORY_ELEMENT');
   		$template->assign ('CATEGORY_UID', $v['uid']);
   		$template->assign ('CATEGORY_ELEMENT', $v['value']);
   	}
   }
   $content = $template->getOutputContent();  
   print $content;
?>

