<?php

if (! defined( 'JAVA_BRIDGE_PATH' )) {
    define( 'JAVA_BRIDGE_PATH', 'JavaBridgePM' );
}
if (! defined( 'JAVA_BRIDGE_PORT' )) {
    define( 'JAVA_BRIDGE_PORT', '8080' );
}
if (! defined( 'JAVA_BRIDGE_HOST' )) {
    define( 'JAVA_BRIDGE_HOST', '127.0.0.1' );
}

/**
 *
 * @package workflow.engine.classes
 */

class JavaBridgePM
{
    public $JavaBridgeDir = JAVA_BRIDGE_PATH;
    public $JavaBridgePort = JAVA_BRIDGE_PORT;
    public $JavaBridgeHost = JAVA_BRIDGE_HOST;

    /**
     * checkJavaExtension
     * check if the java extension was loaded.
     *
     *
     * @return true or false
     */
    public function checkJavaExtension ()
    {
        try {
            if (! extension_loaded( 'java' )) {
                if (! (@include_once ("java/Java.inc"))) {
                    $urlJavaInc = "http://$this->JavaBridgeHost:$this->JavaBridgePort/$this->JavaBridgeDir/java/Java.inc";
                    @include_once ($urlJavaInc);
                    $includedFiles = get_included_files();
                    $found = false;
                    foreach ($includedFiles as $filename) {
                        if ($urlJavaInc == $filename) {
                            $found = true;
                        }
                    }
                    if (! $found) {
                        throw new Exception( 'The PHP/Java Bridge is not defined' );
                    }

                }
                return true;
            }

            if (! function_exists( "java_get_server_name" )) {
                throw new Exception( 'The loaded java extension is not the PHP/Java Bridge' );
            }

            return true;
        } catch (Exception $e) {
            throw new Exception( 'Error in checkJavaExtension: ' . $e->getMessage() );
        }
    }

    /**
     * convert a php value to a java one...
     *
     * @param string $value
     * @param string $className
     * @return s boolean success
     */
    public function convertValue ($value, $className)
    {
        // if we are a string, just use the normal conversion
        // methods from the java extension...
        try {
            if ($className == 'java.lang.String') {
                $temp = new Java( 'java.lang.String', $value );
                return $temp;
            } elseif ($className == 'java.lang.Boolean' || $className == 'java.lang.Integer' || $className == 'java.lang.Long' || $className == 'java.lang.Short' || $className == 'java.lang.Double' || $className == 'java.math.BigDecimal') {
                $temp = new Java( $className, $value );
                return $temp;
            } elseif ($className == 'java.sql.Timestamp' || $className == 'java.sql.Time') {
                $temp = new Java( $className );
                $javaObject = $temp->valueOf( $value );
                return $javaObject;
            }
        } catch (Exception $err) {
            echo ('unable to convert value, ' . $value . ' could not be converted to ' . $className);
            return false;
        }

        echo ('unable to convert value, class name ' . $className . ' not recognised');
        return false;
    }

    /**
     * generateJrxmlFromDynaform
     *
     * @param string $outDocUid
     * @param string $dynaformUid
     * @param object $template
     * @return void
     */
    public function generateJrxmlFromDynaform ($outDocUid, $dynaformUid, $template)
    {
        require_once 'classes/model/Dynaform.php';
        $dyn = new Dynaform();
        $aFields = $dyn->load( $dynaformUid );
        $xmlFields = $dyn->getDynaformFields( $dynaformUid );

        $reportTpl = PATH_TPL . 'javaBridgePM/classic.xml';
        $reportFilename = PATH_DYNAFORM . $aFields['PRO_UID'] . PATH_SEP . $outDocUid . '.jrxml';
        foreach ($xmlFields as $key => $val) {
            if ($val->type == 'submit' || $val->type == 'button' || $val->type == 'title' || $val->type == 'subtitle') {
                unset( $xmlFields[$key] );
            }
        }

        //$sqlSentence = 'SELECT * from ' . $tableName;
        $sqlSentence = 'dynaform/';

        $template = new TemplatePower( $reportTpl );
        $template->prepare();
        $template->assign( 'sqlSentence', $sqlSentence );
        $template->assign( 'tableName', $aFields['DYN_TITLE'] );
        $template->assign( 'heightDetail', count( $xmlFields ) * 15 + 20 );
        $template->assign( 'PAGE_NUMBER', '{PAGE_NUMBER}' );

        $logoReporte = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/images/processmaker.logo.jpg';
        $template->assign( 'logoReporte', $logoReporte );

        foreach ($xmlFields as $key => $val) {
            $template->newBlock( 'fields' );
            $template->assign( 'fieldName', $key );
        }

        $posX = 140;
        $posLabelX = 5;
        $posY = 10;
        foreach ($xmlFields as $key => $val) {
            $template->newBlock( 'detailFields' );
            $template->assign( 'fieldName', '{' . $key . '}' );
            $template->assign( 'fieldLabel', $key );
            $template->assign( 'labelPosX', $posLabelX );
            $template->assign( 'fieldPosX', $posX );
            $template->assign( 'fieldPosY', $posY );
            $posY += 15;
        }

        $content = $template->getOutputContent();
        $iSize = file_put_contents( $reportFilename, $content );
        printf( "saved %s bytes in file %s \n", $iSize, $reportFilename );
    }
}
