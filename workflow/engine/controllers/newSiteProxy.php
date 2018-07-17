<?php

use ProcessMaker\Core\Installer;

class newSiteProxy extends HttpProxyController
{

    public function testingNW ($params) {
        if (isset( $_POST['NW_TITLE'] )) {
            $action = (isset( $_POST['action'] )) ? trim( $_POST['action'] ) : 'test';
            $ao_db_drop = (isset( $_POST['AO_DB_DROP'] )) ? true : false;

            $name = trim( $_POST['NW_TITLE'] );
            $inst = new Installer();
            if ($inst->isset_site($name)) {
                if($ao_db_drop === true){
                   if(!file_exists(PATH_DATA . "sites/" . $name)){
                        $this->error = true;
                        $this->message = 'We can not overwrite this workspace because the workspace '.$name.' does not exist please check the lower case and upper case.';
                        return;
                   }
                } else {
                    $this->error = true;
                    return;
                }
            }
            $user = (isset($_POST['NW_USERNAME'])) ? trim($_POST['NW_USERNAME']) : 'admin';
            $pass = (isset($_POST['NW_PASSWORD'])) ? trim($_POST['NW_PASSWORD']) : 'admin';
            $pass1 = (isset($_POST['NW_PASSWORD2'])) ? trim($_POST['NW_PASSWORD2']) : 'admin';


            $ao_db_wf = (isset( $_POST['AO_DB_WF'] )) ? $_POST['AO_DB_WF'] : false;
            $ao_db_rb = (isset( $_POST['AO_DB_RB'] )) ? $_POST['AO_DB_RB'] : false;
            $ao_db_rp = (isset( $_POST['AO_DB_RP'] )) ? $_POST['AO_DB_RP'] : false;

            $result = $inst->create_site( Array ('isset' => true,'name' => $name,'admin' => Array ('username' => $user,'password' => $pass
            ),'advanced' => Array ('ao_db_drop' => $ao_db_drop,'ao_db_wf' => $ao_db_wf,'ao_db_rb' => $ao_db_rb,'ao_db_rp' => $ao_db_rp
            )
            ), ($action === 'create') ? true : false );
            $result['result']['admin']['password'] = ($pass === $pass1) ? true : false;
            $result['result']['action'] = $action;
            $_SESSION['NW_PASSWORD']  = $pass;
            $_SESSION['NW_PASSWORD2'] = $pass1;
            //$json = new Services_JSON();
            //G::pr($result['result']['database']);G::pr($action);
            $dbWf = $result['result']['database']['ao']['ao_db_wf']['status'];
            $dbRb = $result['result']['database']['ao']['ao_db_rb']['status'];
            $dbRp = $result['result']['database']['ao']['ao_db_rp']['status'];
            $wsAction = ($action != '') ? 1 : 0;
            if ($dbWf && $action) {
                $this->success = true;
                //echo $json->encode($result);
            } else {
                //the site does not available
                $this->error = true;
                $this->message = $result['result']['database']['ao']['ao_db_wf']['message'];
                //$this->message .= ', ' . $result['result']['database']['ao']['ao_db_rb']['message'];
                //$this->message .= ', ' . $result['result']['database']['ao']['ao_db_rp']['message'];
            }
        } else {
            $this->error = true;
        }
    }

   /* public function creatingNW ($params)
    {
        G::pr( $_POST );
        G::pr( "krlossss" );
    }*/
}

