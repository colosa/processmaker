<?php
try {
  $oHeadPublisher = &headPublisher::getSingleton();
  
  $oHeadPublisher->addContent("{className}/{className}Application3"); //Adding a html file .html.
  $oHeadPublisher->addExtJsScript("{className}/{className}Application3", false); //Adding a javascript file .js

  G::RenderPage("publish", "extJs");
} catch (Exception $e) {
  $G_PUBLISH = new Publisher;
  
  $aMessage["MESSAGE"] = $e->getMessage();
  $G_PUBLISH->AddContent("xmlform", "xmlform", "{className}/messageShow", "", $aMessage);
  G::RenderPage("publish", "blank");
}
?>