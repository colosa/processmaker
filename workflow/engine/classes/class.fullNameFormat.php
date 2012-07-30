<?php
  G::LoadClass("configuration");

/**
 * FullNameFormat class
 * @package workflow.engine.ProcessMaker
 */

class FullNameFormat
{
  /**
   * Get full name.
   *
   * @param array $aUser
   * @return $fullName
   */
  
  public static function getFullName($aUser)
  {
    $c = new Configurations();
    $getFormats = $c->getFormats();

    // Remove userid and create default.
    switch($getFormats['format']) {
      case '@firstName @lastName':
      case '@firstName @lastName (@userName)':
      case '@userName (@firstName @lastName)':
        $fullNameFormat = '@firstName @lastName';
        break;
      case '@lastName @firstName':
        $fullNameFormat = '@lastName @firstName';
        break;
      case '@lastName, @firstName':
      case '@lastName, @firstName (@userName)':
        $fullNameFormat = '@lastName, @firstName';
        break;
      default:
        $fullNameFormat = '@firstName @lastName';
    }

    $fullName = G::getFormatUserList($fullNameFormat, $aUser);
    return $fullName;
  }
}
