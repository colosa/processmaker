<?php
/**
 * class.{className}.php
 *  
 */

class {className}Plugin extends PMPlugin {
  
  function {className}Plugin($sNamespace, $sFilename = null) {
    $res = parent::PMPlugin($sNamespace, $sFilename);
    $this->sFriendlyName = "Plugin for skin {className} ";
    $this->sDescription  = "{description}";
    $this->sPluginFolder = "{className}";
    $this->sSetupPage    = "{className}";
    $this->iVersion      = {version};
    $this->iPMVersion    = "{PMversion}";
    $this->aWorkspaces   = null;
    return $res;
  }

  function install() {
    //Nothing
  }

  function setup() {
    //Nothing
  }

  function xcopy ( $pathSource, $pathTarget ) {
  	G::mk_dir ($pathTarget);
    if ($handle = opendir( $pathSource )) {
      while ( false !== ($file = readdir($handle))) {
        if ( substr($file,0,1) != '.' && !is_dir ($file)  ) {
          $content = file_get_contents ( $pathSource . $file );
          $filename = $pathTarget . $file ;
          file_put_contents ( $filename, $content );
        }
      }
      closedir($handle);
    }
  }
  
  function enable() {
  	$this->xcopy ( 
  	        PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . 'data' . PATH_SEP, 
  	        PATH_SKINS );

  	$this->xcopy ( 
  	        PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . 'data' . PATH_SEP . 'public_html'. PATH_SEP ,
  	        PATH_HTML . 'skins' . PATH_SEP . $this->sPluginFolder. PATH_SEP );

  	$this->xcopy ( 
  	        PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . 'data' . PATH_SEP . 'public_html'. PATH_SEP. 'images'. PATH_SEP ,
  	        PATH_HTML . 'skins' . PATH_SEP . $this->sPluginFolder. PATH_SEP . 'images'. PATH_SEP );
  }

  function disable() {
    //delete from engine
    $this->delete(PATH_SKINS . $this->sPluginFolder . '.php',  true);
    $this->delete(PATH_SKINS . $this->sPluginFolder . '.html', true);
    $this->delete(PATH_SKINS . $this->sPluginFolder . '.cnf',  true);

    //delete directory
    G::rm_dir(PATH_HTML . 'skins' . PATH_SEP . $this->sPluginFolder  );
  }

}

$oPluginRegistry =& PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('{className}', __FILE__);
