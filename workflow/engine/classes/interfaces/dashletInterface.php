<?php

interface DashletInterface {

  public static function getAdditionalFields($className);
  public static function getXTemplate($className);
  public function setup($dasInsUid);
  public function render();

}