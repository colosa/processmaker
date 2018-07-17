<?php



class DashletProcessMakerEnterprise implements DashletInterface
{

    const version = '1.0';

    public static function getAdditionalFields($className)
    {
        $additionalFields = array();

        return $additionalFields;
    }

    public static function getXTemplate($className)
    {
        return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
    }

    public function setup($config)
    {
        return true;
    }

    public function render($width = 300)
    {
        $path = PATH_TPL . "/dashboard/dashletProcessMakerEnterprisePm3.html";
        $html = file_get_contents($path);
        echo $html;
    }

}
