<?php

$w = 350;
$h = 90;

//how many task, single task or multiple
$t = isset( $_GET['t'] ) ? $_GET['t'] : 's';
if ($t != 's') {
    $t = 'm';
}

    //when occurs, after time elapses or when starting
$o = isset( $_GET['o'] ) ? $_GET['o'] : 's';
if ($t != 's') {
    $t = 'a';
}

    //status
$s = isset( $_GET['s'] ) ? $_GET['s'] : 'a';
if ($s != 'a') {
    $s = 'i';
}

    //timeUnit
$timeunit = isset( $_GET['t'] ) ? $_GET['t'] : 'Days';
$timeunit = ucfirst( strtolower( $timeunit ) );

//estimated
$estimated = abs( isset( $_GET['e'] ) ? (($timeunit == 'Hours') ? round( $_GET['e'] / 24, 2 ) : $_GET['e']) : '1' );

//when
$when = isset( $_GET['w'] ) ? $_GET['w'] : '0';

$im = imagecreate( $w, $h );
$bg = imagecolorallocate( $im, 0xFF, 0xFF, 0xFF );
$fg = imagecolorallocate( $im, 0x00, 200, 0x00 );
$sc = imagecolorallocate( $im, 200, 0, 0 );
$gray = imagecolorallocate( $im, 180, 180, 180 );
$red = imagecolorallocate( $im, 200, 0, 0 );
$green = imagecolorallocate( $im, 0, 200, 0 );
$blue = imagecolorallocate( $im, 0, 0, 200 );
$black = imagecolorallocate( $im, 0, 0, 0 );

imagerectangle( $im, 0, 0, $w - 1, $h - 1, $gray );

//  $incM = $media/60;


//  $mean = 60*$incM;
//  $d = $varianza;
//  if ( $d == 0 ) $d = 0.0001;


//	$val1 = 1 / ( sqrt( 2*pi() *$d*$d ));
// 	$val2 =  -( pow($mean-$mean,2) )/ (pow($d,2));
// 	$y = $val1  * exp ( $val2 );
//  $incY = 80/$y;


//   $range = 90/ $d;
//   $offsetX = 100 - $mean;
//   $antY = null;
//  for ( $x = $mean -$range*$d ; $x <= $mean +$range*$d; $x++ ) {
//  	$val1 = 1 / ( sqrt( 2*pi() *$d*$d ));
//  	$val2 =  -( pow($x-$mean,2) )/ (pow($d,2));
//  	$y = $val1  * exp ( $val2 )*$incY;
//  	if ( $antY != null )
//    imageline($im, $x-1+$offsetX, $h - $antY-15, $x+$offsetX, $h-$y-15, $blue);
//    $antY = $y;
//    imageline($im, $x +$mean, $h , $x + $mean, $h-1, $red);
//  }


function drawTask ($im, $x1, $x2, $y, $h)
{
    global $w;
    $blue = imagecolorallocate( $im, 160, 160, 180 );
    $gray = imagecolorallocate( $im, 100, 100, 100 );
    $black = imagecolorallocate( $im, 0, 0, 0 );

    for ($i = $y; $i < $h; $i += 2) {
        imageline( $im, $x1, $i, $x1, $i, $gray );
        imageline( $im, $x2, $i, $x2, $i, $gray );
    }

    for ($i = $x1; $i < $x2; $i += 2) {
        imageline( $im, $i, $y - 10, $i, $y, $blue );
    }
    imagerectangle( $im, $x1, $y - 10, $x2, $y, $black );
}
;

function smallTask ($im, $x1, $x2, $y)
{
    $blue = imagecolorallocate( $im, 160, 160, 180 );
    $black = imagecolorallocate( $im, 0, 0, 0 );

    for ($i = $x1; $i < $x2; $i += 2) {
        imageline( $im, $i, $y - 8, $i, $y, $blue );
    }
    imagerectangle( $im, $x1, $y - 9, $x2 - 1, $y, $black );
}

function drawMultipleTask ($im, $x1, $x2, $y, $h)
{
    global $w;
    $terca = ($x2 - $x1) / 3;
    $blue = imagecolorallocate( $im, 160, 160, 180 );
    $gray = imagecolorallocate( $im, 100, 100, 100 );
    $black = imagecolorallocate( $im, 0, 0, 0 );

    for ($i = $y; $i < $h; $i += 2) {
        imageline( $im, $x2, $i, $x2, $i, $gray );
    }
    for ($i = $y - 10; $i < $h; $i += 2) {
        imageline( $im, $x1, $i, $x1, $i, $gray );
    }

    smallTask( $im, $x1 + 0 * $terca, $x1 + 1 * $terca, $y - 12 );
    smallTask( $im, $x1 + 1 * $terca, $x1 + 2 * $terca, $y - 6 );
    smallTask( $im, $x1 + 2 * $terca, $x1 + 3 * $terca, $y );
}
;

function drawTimerEvent ($im, $x1, $y1, $h)
{
    $blue = imagecolorallocate( $im, 160, 160, 180 );
    $red = imagecolorallocate( $im, 200, 100, 0 );
    $gray = imagecolorallocate( $im, 100, 100, 100 );
    $black = imagecolorallocate( $im, 0, 0, 0 );
    $yellow = imagecolorallocate( $im, 240, 240, 220 );

    for ($i = $y1 + 15; $i < $h; $i += 2) {
        imageline( $im, $x1, $i, $x1, $i, $gray );
    }

    ImageEllipse( $im, $x1, $y1, 26, 26, $black );
    ImageEllipse( $im, $x1, $y1, 22, 22, $black );
    ImageFilledEllipse( $im, $x1, $y1, 16, 16, $yellow );
    ImageEllipse( $im, $x1, $y1, 16, 16, $red );
    imageline( $im, $x1, $y1 - 8, $x1, $y1 + 8, $red );
    //imageline($im, $x1  , $y1+8 , $x1, $y1+6,  $red);
    imageline( $im, $x1 - 8, $y1, $x1 + 8, $y1, $red );
    imageline( $im, $x1 - 7, $y1 - 4, $x1 + 7, $y1 + 4, $red );
    imageline( $im, $x1 - 4, $y1 - 7, $x1 + 4, $y1 + 7, $red );
    imageline( $im, $x1 + 7, $y1 - 4, $x1 - 7, $y1 + 4, $red );
    imageline( $im, $x1 + 4, $y1 - 7, $x1 - 4, $y1 + 7, $red );
    ImageFilledEllipse( $im, $x1, $y1, 10, 10, $yellow );
    imageline( $im, $x1 - 1, $y1 + 1, $x1 + 1, $y1 - 5, $red );
    imageline( $im, $x1 - 1, $y1 + 1, $x1 + 3, $y1 + 1, $red );

}

imageline( $im, 15, $h - 19, $w - 15, $h - 19, $red );
imageline( $im, $w - 23, $h - 23, $w - 15, $h - 19, $red );
imageline( $im, $w - 23, $h - 15, $w - 15, $h - 19, $red );
imagestring( $im, 2, $w - 30, $h - 37, 'Days', $red );

if ($estimated == 0) {
    $s = 'i';
    header( "Content-Type: image/png" );
    imagepng( $im );
    die();
}

if ($t == 's') {
    drawTask( $im, 80, 220, $h - 30, $h - 15 );
} else {
    drawMultipleTask( $im, 80, 220, $h - 30, $h - 15 );
}

    //the zero
imagestring( $im, 3, 80 - 4, $h - 16, '0', $black );
//the estimated
imagestring( $im, 2, 220 - 4, $h - 16, $estimated, $black );

//when is negative and the event occurs at starting, then this event never will occurs
if ($when < 0 && $o == 's') {
    $xTimer = 30;
    imagestring( $im, 2, $xTimer - 8, $h - 16, $when, $black );
    $s = 'i';
}

//when is negative and the event occurs after, then this event will occurs
if ($when < 0 && $o == 'a') {
    if (abs( $when ) > abs( $estimated )) {
        //this event is before the start of the task, so will never occurs
        $xTimer = 30;
        $sWhen = abs( $when ) - abs( $estimated );
        imagestring( $im, 2, $xTimer - 8, $h - 16, $sWhen, $black );
        $s = 'i';
    }
    if (abs( $when ) < abs( $estimated )) {
        //this event is after the start of the task, drawing
        $xTimer = 170;
        $sWhen = $estimated + $when;
        imagestring( $im, 2, $xTimer - 4, $h - 16, $sWhen, $black );
    }
    if (abs( $when ) == abs( $estimated )) {
        //this event is exactly at starting
        $xTimer = 80;
        $sWhen = $estimated + $when;
        imagestring( $im, 2, $xTimer - 4, $h - 16, $sWhen, $black );
    }
}

//when is positive and the event occurs after, then this event will occurs
if ($when > 0 && $o == 'a') {
    $xTimer = 270;
    $sWhen = $estimated + $when;
    imagestring( $im, 2, $xTimer - 4, $h - 16, $sWhen, $black );
}

//when is positive and the event occurs starting, then this event will occurs
if ($when > 0 && $o == 's') {
    if (abs( $when ) < abs( $estimated )) {
        $xTimer = 140;
    }
    if (abs( $when ) > abs( $estimated )) {
        $xTimer = 270;
    }
    if (abs( $when ) == abs( $estimated )) {
        $xTimer = 220;
    }
    imagestring( $im, 2, $xTimer - 4, $h - 16, $when, $black );
}

if ($when == 0) {
    $xTimer = ($o == 's') ? 80 : 220;
}

if ($s == 'a') {
    drawTimerEvent( $im, $xTimer, $h - 70, $h - 15 );
}

header( "Content-Type: image/png" );
imagepng( $im );
die();

