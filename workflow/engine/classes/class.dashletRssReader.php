<?php

require_once 'classes/interfaces/dashletInterface.php';

class dashletRssReader implements DashletInterface
{

    const version = '1.0';

    public static function getAdditionalFields ($className)
    {
        $additionalFields = array ();

        $urlFrom = new stdclass();
        $urlFrom->xtype = 'textfield';
        $urlFrom->name = 'DAS_URL';
        $urlFrom->fieldLabel = 'Url';
        $urlFrom->width = 320;
        $urlFrom->maxLength = 200;
        $urlFrom->allowBlank = false;
        $urlFrom->value = "http://license.processmaker.com/syspmLicenseSrv/en/green/services/rssAP";
        $additionalFields[] = $urlFrom;

        return $additionalFields;
    }

    public static function getXTemplate ($className)
    {
        return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
    }

    public function setup ($config)
    {
        $this->urlFrom = isset( $config['DAS_URL'] ) ? $config['DAS_URL'] : "http://license.processmaker.com/syspmLicenseSrv/en/green/services/rssAP";
        return true;
    }

    public function render ($width = 300)
    {
        $pCurl = curl_init();
        curl_setopt( $pCurl, CURLOPT_URL, $this->urlFrom );
        curl_setopt( $pCurl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $pCurl, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt( $pCurl, CURLOPT_AUTOREFERER, true );
        //To avoid SSL error
        curl_setopt( $pCurl, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $pCurl, CURLOPT_SSL_VERIFYPEER, 0 );

        //To avoid timeouts
        curl_setopt( $pCurl, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $pCurl, CURLOPT_TIMEOUT, 20 );

        curl_setopt( $pCurl, CURLOPT_NOPROGRESS, false );
        curl_setopt( $pCurl, CURLOPT_VERBOSE, true );

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt( $pCurl, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '') );
            if ($sysConf['proxy_port'] != '') {
                curl_setopt( $pCurl, CURLOPT_PROXYPORT, $sysConf['proxy_port'] );
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt( $pCurl, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : '') );
            }
            curl_setopt( $pCurl, CURLOPT_HTTPHEADER, array ('Expect:'
            ) );
        }

        $self->rss = @simplexml_load_string( curl_exec( $pCurl ) );
        if ($self->rss) {
            $index = 0;
            $render = '';
            $self->items = $self->rss->channel->item;
            if (count( $self->rss->channel ) != 0) {
                $status = 'true';
                foreach ($self->items as $self->item) {
                    $self->title = $self->item->title;
                    $self->link = $self->item->link;

                    $self->des = $self->item->description;
                    $render[] = array ('link' => '<a href="' . $self->link . '" target="_blank">' . $self->title . '</a><br/>','description' => $self->des . '<br/><hr>'
                    );
                    $index ++;
                }
            } else {
                $status = 'Error';
                $render[] = array ('link' => 'Error','description' => "Unable to parse XML"
                );
            }
        } else {
            $status = 'Error';
            $render[] = array ('link' => 'Error','description' => "Unable to parse XML"
            );
        }
        G::verifyPath( PATH_SMARTY_C, true );
        $smarty = new Smarty();
        $smarty->template_dir = PATH_CORE . 'templates/dashboard/';
        $smarty->compile_dir = PATH_SMARTY_C;

        try {
            $smarty->assign( 'url', $this->urlFrom );
            $smarty->assign( 'render', $render );
            $smarty->assign( 'status', $status );
        } catch (Exception $ex) {
            print $item->key;
        }
        $smarty->display( 'dashletRssReaderTemplate.html', null, null );

    }

}