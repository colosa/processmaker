<?php

require_once 'classes/interfaces/dashletInterface.php';

class dashletProcessMakerCommunity implements DashletInterface
{

    const version = '1.0';

    public static function getAdditionalFields ($className)
    {
        $additionalFields = array ();

        return $additionalFields;
    }

    public static function getXTemplate ($className)
    {
        return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
    }

    public function setup ($config)
    {
        return true;
    }

    public function render ($width = 300)
    {
        $html = "
    <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">

    <html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

      <title></title>

      <style type=\"text/css\">
      body{
        margin: 0;
        padding: 0;

        background: #FFFFFF;
        font: 100% arial, verdana, helvetica, sans-serif;
        color: #000000;
      }

      #container{
        margin: 0 auto;


      }

      .clearf{
        clear: both;

        height: 0;
        line-height: 0;
        font-size: 0;
      }

      .icon{
        float:left;

        margin-left: 5%;

        width: 13%;
      }

      .description{
        float: right;

        margin-right: 1%;

        width: 79.5%;

        font-size: 64%;
        text-align : justify;
      }

      .icon img{
        width: 35px;
      }

      .description strong{
        color: #2C2C2C;
      }

      .description a{
        color: #1A4897;
        font-weight: bold;
      }

      .icon, .description{
        margin-top: 0.65em;
      }
      </style>
    </head>
    <body>

    <div id=\"container\">
      <div class=\"icon\"><img src=\"/images/dashlets/community_forum.png\"/></div>
      <div class=\"description\">
        <strong>FORUM - </strong><a href=\"http://forum.processmaker.com/\" target=\"_blank\" title=\"http://forum.processmaker.com\">http://forum.processmaker.com</a>
        <br />
       Discuss Processker issues, interact with the PMOS community, and get support from fellow developers and community members in the ProcessMaker Forum.
      </div>
      <div class=\"clearf\"></div>

      <div class=\"icon\"><img src=\"/images/dashlets/community_wiki.png\" style=\"border: medium none;\"></div>
      <div class=\"description\">
        <strong>WIKI - </strong><a href=\"http://wiki.processmaker.com/\" target=\"_blank\" title=\"http://wiki.processmaker.com\">http://wiki.processmaker.com</a>
        <br />
        The Wiki is your first stop for ProcessMaker information, including user guides, documentation, community projects, release notes, and FAQ.
      </div>
      <div class=\"clearf\"></div>

      <div class=\"icon\"><img src=\"/images/dashlets/community_blog.png\" style=\"border: medium none;\"></div>
      <div class=\"description\">
        <strong>BLOG - </strong><a href=\"http://blog.processmaker.com/\" target=\"_blank\" title=\"http://blog.processmaker.com\">http://processmakerblog.com</a>
        <br />
        Get our BPM tips in the ProcessMaker blog.
      </div>
      <div class=\"clearf\"></div>

      <div class=\"icon\"><img src=\"/images/dashlets/community_bug_tracker.png\" style=\"border: medium none;\"></div>
      <div class=\"description\">
        <strong>BUG TRACKER - </strong><a href=\"http://bugs.processmaker.com/\" target=\"_blank\">http://bugs.processmaker.com</a>
        <br />
        Help our development team to improve ProcessMaker by reporting your bugs and issues in the Bug Tracker.&nbsp; Monitor and track issue reports and solutions.
      </div>
      <div class=\"clearf\"></div>
    </div>

    </body>
    </html>
    ";

        echo $html;
    }

}