<?php
// $Header: /cvsroot/html2ps/demo/html2ps.php,v 1.8 2007/03/15 18:37:36 Konstantin Exp $

error_reporting(E_ALL);
ini_set("display_errors","1");
@set_time_limit(10000);

require_once('generic.param.php');
require_once('../config.inc.php');
require_once(HTML2PS_DIR.'pipeline.factory.class.php');

ini_set("user_agent", DEFAULT_USER_AGENT);

$g_baseurl = trim(get_var('URL', $_REQUEST));

if ($g_baseurl === "") {
  die("Please specify URL to process!");
}

// Add HTTP protocol if none specified
if (!preg_match("/^https?:/",$g_baseurl)) {
  $g_baseurl = 'http://'.$g_baseurl;
}

$g_css_index = 0;

// Title of styleshee to use (empty if no preferences are set)
$g_stylesheet_title = "";

$GLOBALS['g_config'] = array('cssmedia'      => get_var('cssmedia', $_REQUEST, 255, "screen"),
                             'media'         => get_var('media', $_REQUEST, 255, "A4"),
                             'scalepoints'   => isset($_REQUEST['scalepoints']),
                             'renderimages'  => isset($_REQUEST['renderimages']),
                             'renderfields'  => isset($_REQUEST['renderfields']),
                             'renderforms'   => isset($_REQUEST['renderforms']),
                             'pslevel'       => (int)get_var('pslevel', $_REQUEST, 1, 3),
                             'renderlinks'   => isset($_REQUEST['renderlinks']),
                             'pagewidth'     => (int)get_var('pixels', $_REQUEST, 10, 800),
                             'landscape'     => isset($_REQUEST['landscape']),
                             'method'        => get_var('method', $_REQUEST, 255, "fpdf"),
                             'margins'       => array(
                                                      'left'    => (int)get_var('leftmargin',   $_REQUEST, 10, 0),
                                                      'right'   => (int)get_var('rightmargin',  $_REQUEST, 10, 0),
                                                      'top'     => (int)get_var('topmargin',    $_REQUEST, 10, 0),
                                                      'bottom'  => (int)get_var('bottommargin', $_REQUEST, 10, 0),
                                                      ),
                             'encoding'      => get_var('encoding', $_REQUEST, 255, ""),
                             'ps2pdf'        => isset($_REQUEST['ps2pdf']),
                             'compress'      => isset($_REQUEST['compress']),
                             'output'        => get_var('output', $_REQUEST, 255, ""),
                             'pdfversion'    => get_var('pdfversion', $_REQUEST, 255, "1.2"),
                             'transparency_workaround' => isset($_REQUEST['transparency_workaround']),
                             'imagequality_workaround' => isset($_REQUEST['imagequality_workaround']),
                             'draw_page_border'        => isset($_REQUEST['pageborder']),
                             'debugbox'      => isset($_REQUEST['debugbox']),
                             'html2xhtml'    => !isset($_REQUEST['html2xhtml']),
                             'mode'          => 'html',
                             'smartpagebreak' => isset($_REQUEST['smartpagebreak'])
                             );

$proxy = get_var('proxy', $_REQUEST, 255, '');

// ========== Entry point
parse_config_file('../html2ps.config');

// validate input data
if ($GLOBALS['g_config']['pagewidth'] == 0) {
  die("Please specify non-zero value for the pixel width!");
};

// begin processing

$g_media = Media::predefined($GLOBALS['g_config']['media']);
$g_media->set_landscape($GLOBALS['g_config']['landscape']);
$g_media->set_margins($GLOBALS['g_config']['margins']);
$g_media->set_pixels($GLOBALS['g_config']['pagewidth']);

// Initialize the coversion pipeline
$pipeline = new Pipeline();

// Configure the fetchers
if (extension_loaded('curl')) {
  require_once(HTML2PS_DIR.'fetcher.url.curl.class.php');
  $pipeline->fetchers = array(new FetcherURLCurl());
  if ($proxy != '') {
    $pipeline->fetchers[0]->set_proxy($proxy);
  };
} else {
  require_once(HTML2PS_DIR.'fetcher.url.class.php');
  $pipeline->fetchers[] = new FetcherURL();
};

// Configure the data filters
$pipeline->data_filters[] = new DataFilterDoctype();
$pipeline->data_filters[] = new DataFilterUTF8($GLOBALS['g_config']['encoding']);
if ($GLOBALS['g_config']['html2xhtml']) {
  $pipeline->data_filters[] = new DataFilterHTML2XHTML();
} else {
  $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
};

$pipeline->parser = new ParserXHTML();

// "PRE" tree filters

$pipeline->pre_tree_filters = array();

$header_html    = get_var('headerhtml', $_REQUEST, 65535, "");
$footer_html    = get_var('footerhtml', $_REQUEST, 65535, "");
$filter = new PreTreeFilterHeaderFooter($header_html, $footer_html);
$pipeline->pre_tree_filters[] = $filter;

if ($GLOBALS['g_config']['renderfields']) {
  $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
};

//

if ($GLOBALS['g_config']['method'] === 'ps') {
  $pipeline->layout_engine = new LayoutEnginePS();
} else {
  $pipeline->layout_engine = new LayoutEngineDefault();
};

$pipeline->post_tree_filters = array();

// Configure the output format
if ($GLOBALS['g_config']['pslevel'] == 3) {
  $image_encoder = new PSL3ImageEncoderStream();
} else {
  $image_encoder = new PSL2ImageEncoderStream();
};

switch ($GLOBALS['g_config']['method']) {
 case 'fastps':
   if ($GLOBALS['g_config']['pslevel'] == 3) {
     $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
   } else {
     $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
   };
   break;
 case 'pdflib':
   $pipeline->output_driver = new OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
   break;
 case 'fpdf':
   $pipeline->output_driver = new OutputDriverFPDF();
   break;
 case 'png':
   $pipeline->output_driver = new OutputDriverPNG();
   break;
 case 'pcl':
   $pipeline->output_driver = new OutputDriverPCL();
   break;
 default:
   die("Unknown output method");
};

// Setup watermark
$watermark_text = get_var('watermarkhtml', $_REQUEST, 65535, "");
$pipeline->output_driver->set_watermark($watermark_text);

if ($watermark_text != "") {
  $dispatcher =& $pipeline->getDispatcher();

  // @TODO: render watermark in observer
  // $dispatcher->add_observer("after-page", new Observer());
};

if ($GLOBALS['g_config']['debugbox']) {
  $pipeline->output_driver->set_debug_boxes(true);
}

if ($GLOBALS['g_config']['draw_page_border']) {
  $pipeline->output_driver->set_show_page_border(true);
}

if ($GLOBALS['g_config']['ps2pdf']) {
  $pipeline->output_filters[] = new OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
}

if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps') {
  $pipeline->output_filters[] = new OutputFilterGZip();
}

if (get_var('process_mode', $_REQUEST) == 'batch') {
  $filename = "batch";
} else {
  $filename = $g_baseurl;
};

switch ($GLOBALS['g_config']['output']) {
 case 0:
   $pipeline->destination = new DestinationBrowser($filename);
   break;
 case 1:
   $pipeline->destination = new DestinationDownload($filename);
   break;
 case 2:
   $pipeline->destination = new DestinationFile($filename);
   break;
};

// Start the conversion

$time = time();
if (get_var('process_mode', $_REQUEST) == 'batch') {
  $batch = get_var('batch', $_REQUEST);

  for ($i=0; $i<count($batch); $i++) {
    if (trim($batch[$i]) != "") {
      if (!preg_match("/^https?:/",$batch[$i])) {
        $batch[$i] = "http://".$batch[$i];
      }
    };
  };

  $status = $pipeline->process_batch($batch, $g_media);
} else {
  $status = $pipeline->process($g_baseurl, $g_media);
};

error_log(sprintf("Processing of '%s' completed in %u seconds", $g_baseurl, time() - $time));

if ($status == null) {
  print($pipeline->error_message());
  error_log("Error in conversion pipeline");
  die();
}

?>