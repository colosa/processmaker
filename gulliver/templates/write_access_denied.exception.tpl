<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title>Whoops, looks like something went wrong.</title>
        <style>
        {literal}
            /* Copyright (c) 2010, Yahoo! Inc. All rights reserved. Code licensed under the BSD License: http://developer.yahoo.com/yui/license.html */
            html{color:#000;background:#FFF;}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}fieldset,img{border:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}q:before,q:after{content:'';}abbr,acronym{border:0;font-variant:normal;}sup{vertical-align:text-top;}sub{vertical-align:text-bottom;}input,textarea,select{font-family:inherit;font-size:inherit;font-weight:inherit;}input,textarea,select{*font-size:100%;}legend{color:#000;}

            html { background: #eee; padding: 10px }
            body { font: 11px Verdana, Arial, sans-serif; color: #333 }
            img { border: 0; }
            .clear { clear:both; height:0; font-size:0; line-height:0; }
            .clear_fix:after { display:block; height:0; clear:both; visibility:hidden; }
            .clear_fix { display:inline-block; }
            * html .clear_fix { height:1%; }
            .clear_fix { display:block; }
            #content { width:970px; margin:0 auto; }
            .exceptionreset, .exceptionreset .block { margin: auto }
            .exceptionreset abbr { border-bottom: 1px dotted #000; cursor: help; }
            .exceptionreset p { font-size:14px; line-height:20px; color:#868686; padding-bottom:20px }
            .exceptionreset strong { font-weight:bold; }
            .exceptionreset a { color:#6c6159; }
            .exceptionreset a img { border:none; }
            .exceptionreset a:hover { text-decoration:underline; }
            .exceptionreset em { font-style:italic; }
            .exceptionreset h1, .exceptionreset h2 { font: 20px Georgia, "Times New Roman", Times, serif }
            .exceptionreset h2 span { background-color: #fff; color: #333; padding: 6px; float: left; margin-right: 10px; }
            .exceptionreset .traces li { font-size:12px; padding: 2px 4px; list-style-type:decimal; margin-left:20px; }
            .exceptionreset .block { background-color:#FFFFFF; padding:10px 28px; margin-bottom:20px;
                -webkit-border-bottom-right-radius: 16px;
                -webkit-border-bottom-left-radius: 16px;
                -moz-border-radius-bottomright: 16px;
                -moz-border-radius-bottomleft: 16px;
                border-bottom-right-radius: 16px;
                border-bottom-left-radius: 16px;
                border-bottom:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
            }
            .exceptionreset .block_exception { background-color:#ddd; color: #333; padding:20px;
                -webkit-border-top-left-radius: 16px;
                -webkit-border-top-right-radius: 16px;
                -moz-border-radius-topleft: 16px;
                -moz-border-radius-topright: 16px;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                border-top:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
            }
            .exceptionreset li a { background:none; color:#868686; text-decoration:none; }
            .exceptionreset li a:hover { background:none; color:#313131; text-decoration:underline; }
            .exceptionreset ol { padding: 10px 0; }
            .exceptionreset h1 { background-color:#FFFFFF; padding: 15px 28px; margin-bottom: 20px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                border: 1px solid #ccc;
            }
        {/literal}
        </style>
    </head>
    <body>
        <div id="content" class="exceptionreset">
            <h1>ProcessMaker Start, looks like something went wrong.</h1>
            <div class="block_exception clear_fix"><h2>
                <abbr title="RuntimeException">RuntimeException</abbr>: 
                Unable to write on ProcessMaker directories, change permissions to run properly.</h2>
            </div>
            <div class="block">
                <ol class="traces list_exception">
                    {foreach from=$files item=file}
                    <li>at <abbr title="{$file}">{$file}</abbr> .... <font color="red">not writable.</font></li>
                    {/foreach}
                </ol>
            </div>
        </div>
    </body>
</html>