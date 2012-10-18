<?php

  $aFields['PRO_UID'] = isset($_GET['PRO_UID'])?$_GET['PRO_UID']:'';

  $G_PUBLISH = new Publisher();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_ChoseType', '', $aFields , SYS_URI.'dynaforms/dynaforms_Edit');

  G::RenderPage( "publish-raw" , "raw" );

