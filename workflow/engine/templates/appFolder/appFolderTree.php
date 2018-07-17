<?php
global $rootFolder;

require_once ( "classes/model/AppFolder.php" );
$oPMFolder = new AppFolder();
if(($rootFolder=='0')||($rootFolder==0)){
  $folderPath['PATH']="/";
}else{
  $folderPath=$oPMFolder->getFolderStructure($rootFolder);
}

$html = '
<div>

	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">

	  <table style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle"><div class="userGroupLink">'.G::LoadTranslation("ID_PM_FOLDER").': <a href="javascript:openPMFolder(\''.$rootFolder.'\',\''.$rootFolder.'\')">'.$folderPath['PATH'].'</a></div></td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>

	';
	$html.='<div class="treeBase" style="width:360px;height: expression( this.scrollHeight > 319 ? \'320px\' : \'auto\' ); /* sets max-height for IE */  max-height: 320px; /* sets max-height value for all standards-compliant browsers */  overflow:hidden;">
	<div class="boxTop">
		<div class="a"></div>
		<div class="b"></div>
		<div class="c"></div>
	</div>

 <!-- <div class="content" style="width:360px;height: expression( this.scrollHeight > 319 ? \'320px\' : \'auto\' ); /* sets max-height for IE */  max-height: 320px; /* sets max-height value for all standards-compliant browsers */  overflow:hidden;"> -->';
  
  //add alvaro
  function looking_for_browser($user_agent) {
      $browsers = array(
           'Opera' => 'Opera',
           'Mozilla Firefox'=> '(Firebird)|(Firefox)',
           'Galeon' => 'Galeon',
           'Mozilla'=>'Gecko',
           'MyIE'=>'MyIE',
           'Lynx' => 'Lynx',
           'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
           'Konqueror'=>'Konqueror',
           'Internet Explorer 8' => '(MSIE 8\.[0-9]+)',
           'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
           'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
           'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
           'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
 );
 foreach($browsers as $browser=>$pattern){
        if (preg_match($pattern, $user_agent))
        return $browser;
     }
 return 'Unknown';
 }

$filter = new InputFilter();
$_SERVER['HTTP_USER_AGENT'] = $filter->xssFilterHard($_SERVER['HTTP_USER_AGENT']);
if((looking_for_browser($_SERVER['HTTP_USER_AGENT'])=='Internet Explorer 8')||(looking_for_browser($_SERVER['HTTP_USER_AGENT'])=='Internet Explorer 7')||(looking_for_browser($_SERVER['HTTP_USER_AGENT'])=='Internet Explorer 6')){
    $html.="
        <div class='content' style='width:360px;height: expression( this.scrollHeight > 319 ? \'320px\' : \'auto\' ); /* sets max-height for IE */  max-height: 320px; /* sets max-height value for all standards-compliant browsers */  overflow:hidden;'>
        <div id='child_$rootFolder'></div>
  </div></div>
  ";

  $html.='
  <div class="boxBottom" style="width:360px;overflow:hidden;">
		<div class="a"></div>
		<div class="b"></div>
		<div class="c"></div>
	</div>
	</div>';

}else{
    $html.="
        <div class='content'>
        <div id='child_$rootFolder'></div>
  </div></div>
  ";
    $html.='
  <div class="boxBottom">
		<div class="a"></div>
		<div class="b"></div>
		<div class="c"></div>
	</div>
	</div>';

}


  //end add

  

	$html.='<div class="treeBase" style="width:360px;height:200px; border		: 0px solid #006699; overflow	: hidden;">
	<div class="boxTop">
		<div class="a"></div>
		<div class="b"></div>
		<div class="c"></div>
	</div>

  <div class="content" > ';
  $html.="<div id='tags_cloud'  ></div>
  </div>
  ";
  $html.='
  <div class="boxBottom">
		<div class="a"></div>
		<div class="b"></div>
		<div class="c"></div>
	</div>
	</div>';



  echo $html;
  ?>