<?php
/**
 * inc.application.php
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

class App
{
  function ForceLogin()
  {
    global $G_MAIN_MENU;
    global $G_SUB_MENU;
    if( $_SESSION['LOGGED_IN'] == false)
    {
      header( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/login.html" ); 
      die();
    }
    else
    {
      $cmptype = $_SESSION['USER_TYPE'];
      switch( $cmptype )
      {
      case 'BUYER':
        $G_MAIN_MENU = "buyer";
        $G_SUB_MENU  = "";
        break;
      case 'PROVIDER':
        $G_MAIN_MENU = "provider";
        $G_SUB_MENU  = "";
        break;
      case 'REINSURANCE':
        $G_MAIN_MENU = "reinsurance";
        $G_SUB_MENU  = "";
        break;
      case 'ADMIN':
        $G_MAIN_MENU = "admin";
        $G_SUB_MENU  = "";
        break;
      case '':
        header( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/login.html" ); 
        die();
	break;
      default:
        $G_MAIN_MENU = "default";
	$G_SUB_MENU  = "";
        break;
      }
    }
  }
  
  function GetPartnerStatus()
  {
    $slipid = $_SESSION['CURRENT_SLIP'];
    $partnerid = $_SESSION['CURRENT_PARTNER'];
    
    $mdbc = new DBConnection();

    $slip = new Slip;
    $slip->SetTo( $mdbc );
    $slip->Load( $slipid );
    $partner = $slip->GetPartner( $partnerid );
    
    $res = $partner->Fields['SLP_PARTNER_STATUS'];
    unset( $partner );
    unset( $slip );
    unset( $mdbc );
    return $res;
  }
  
  function SetPartnerStatus( $intStatus = 0 )
  {
    $slipid = $_SESSION['CURRENT_SLIP'];
    $partnerid = $_SESSION['CURRENT_PARTNER'];
    
    $mdbc = new DBConnection();

    $slip = new Slip;
    $slip->SetTo( $mdbc );
    $slip->Load( $slipid );
    $partner = $slip->GetPartner( $partnerid );
    
    $partner->Fields = NULL;
    $partner->Fields['UID_SLIP'] = $slipid;
    $partner->Fields['UID_REINSURANCE'] = $partnerid;
    $partner->Fields['SLP_PARTNER_STATUS'] = $intStatus;
    $partner->Fields['SLP_PARTNER_UPDATED'] = G::CurDate();
    $partner->Save();
        
    unset( $partner );
    unset( $slip );
    unset( $mdbc );
  }

}

?>