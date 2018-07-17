<?php


$geshi = new GeSHi($_POST["code"], 'php');
$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
$geshi->set_line_style('background: #f0f0f0;');
echo $geshi->parse_code();
?>
