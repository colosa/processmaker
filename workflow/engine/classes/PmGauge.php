<?php


class PmGauge
{

    /**
     * width
     */
    public $w = 610;

    /**
     * height
     */
    public $h = 300;

    /**
     * value of gauge
     */
    public $value = 50;

    /**
     * maxValue
     */
    public $maxValue = 100;

    /**
     * redFrom
     */
    public $redFrom = 80;

    /**
     * redTo
     */
    public $redTo = 100;

    /**
     * yellowFrom
     */
    public $yellowFrom = 60;

    /**
     * yellowTo
     */
    public $yellowTo = 80;

    /**
     * greenFrom
     */
    public $greenFrom = 0;

    /**
     * greenTo
     */
    public $greenTo = 60;

    /**
     * centerLabel, the label in the middle of the gauge
     */
    public $centerLabel = '';

    public function render()
    {
        $this->h = $this->w / 2;
        $im = imagecreatetruecolor($this->w, $this->h);
        $width = $this->w;
        $height = $this->h;
        $center_x = intval($width / 2);
        $center_y = intval($height / 2);

        //gauge color
        $bgcolor = ImageColorAllocate($im, 247, 247, 247);
        $extRing = ImageColorAllocate($im, 214, 214, 214);
        $blueRing = ImageColorAllocate($im, 70, 132, 238);
        $blueRingLine = ImageColorAllocate($im, 106, 114, 127);
        $arrowBody = ImageColorAllocate($im, 228, 114, 86);
        $arrowLine = ImageColorAllocate($im, 207, 74, 42);
        $redArc = ImageColorAllocate($im, 220, 57, 18);
        $yellowArc = ImageColorAllocate($im, 255, 153, 0);

        $black = ImageColorAllocate($im, 0, 0, 0);
        $white = ImageColorAllocate($im, 255, 255, 255);
        $gray = ImageColorAllocate($im, 190, 190, 190);

        $fontArial = PATH_THIRDPARTY . 'html2ps_pdf/fonts/arial.ttf';

        ImageFilledRectangle($im, 0, 0, $width - 1, $height - 1, $white);
        ImageRectangle($im, 0, 0, $width - 1, $height - 1, $gray);

        //center coords
        $cX = intval($this->w / 2);
        //$cX = intval($this->w /4);
        $cY = intval($this->h / 2);

        //diameter for gauge
        $diameter = intval($this->h * 4 / 5);

        $this->renderGauge($im, $cX, $cY, $diameter);

        /*
          //center coords
          $cX = intval($this->w * 3/4);
          $cY = intval($this->h /2);

          //diameter for gauge
          $diameter = intval( $this->h * 4/5 );

          $this->renderGauge($im, $cX, $cY, $diameter);
         */
        Header("Content-type: image/png");
        ImagePng($im);
    }

    public function renderGauge($im, $cX, $cY, $diameter)
    {
        //gauge color
        $bgcolor = ImageColorAllocate($im, 247, 247, 247);
        $extRing = ImageColorAllocate($im, 214, 214, 214);
        $blueRing = ImageColorAllocate($im, 70, 132, 238);
        $blueRingLine = ImageColorAllocate($im, 106, 114, 127);
        $arrowBody = ImageColorAllocate($im, 228, 114, 86);
        $arrowLine = ImageColorAllocate($im, 207, 74, 42);
        $redArc = ImageColorAllocate($im, 220, 57, 18);
        $yellowArc = ImageColorAllocate($im, 255, 153, 0);
        $greenArc = ImageColorAllocate($im, 0, 136, 0);

        $black = ImageColorAllocate($im, 0, 0, 0);
        $white = ImageColorAllocate($im, 255, 255, 255);
        $gray = ImageColorAllocate($im, 190, 190, 190);

        $fontArial = PATH_THIRDPARTY . 'html2ps_pdf/fonts/arial.ttf';

        $dX = intval($diameter * 8 / 7); //for now ratio aspect is 8:7
        $dY = intval($diameter);
        $dXRing = intval($dX * 0.90);
        $dYRing = intval($dY * 0.90);

        $dXRingColor = intval($dX * 0.86);
        $dYRingColor = intval($dY * 0.86);

        $dXRingCenter = intval($dX * 0.66);
        $dYRingCenter = intval($dY * 0.66);

        imagefilledellipse($im, $cX, $cY, $dX, $dY, $extRing);

        imagefilledellipse($im, $cX, $cY, $dXRing, $dYRing, $bgcolor);

        //drawing the red arc
        if ($this->redFrom > $this->maxValue) {
            $this->redFrom = $this->maxValue;
        }
        if ($this->redTo > $this->maxValue) {
            $this->redTo = $this->maxValue;
        }
        if ($this->yellowFrom > $this->maxValue) {
            $this->yellowFrom = $this->maxValue;
        }
        if ($this->yellowTo > $this->maxValue) {
            $this->yellowTo = $this->maxValue;
        }
        if ($this->greenFrom > $this->maxValue) {
            $this->greenFrom = $this->maxValue;
        }
        if ($this->greenTo > $this->maxValue) {
            $this->greenTo = $this->maxValue;
        }

        $redFrom = $this->redFrom / $this->maxValue * 300 - 240;
        $redTo = $this->redTo / $this->maxValue * 300 - 240;
        $yellowFrom = $this->yellowFrom / $this->maxValue * 300 - 240;
        $yellowTo = $this->yellowTo / $this->maxValue * 300 - 240;
        $greenFrom = $this->greenFrom / $this->maxValue * 300 - 240;
        $greenTo = $this->greenTo / $this->maxValue * 300 - 240;

        if ($this->redFrom != $this->redTo || $this->redTo != $this->maxValue) {
            imagefilledarc($im, $cX, $cY, $dXRingColor, $dYRingColor, $redFrom, $redTo, $redArc, IMG_ARC_PIE);
        }
        if ($this->yellowFrom != $this->yellowTo || $this->yellowTo != $this->maxValue) {
            imagefilledarc($im, $cX, $cY, $dXRingColor, $dYRingColor, $yellowFrom, $yellowTo, $yellowArc, IMG_ARC_PIE);
        }
        if ($this->greenFrom != $this->greenTo || $this->greenTo != $this->maxValue) {
            imagefilledarc($im, $cX, $cY, $dXRingColor, $dYRingColor, $greenFrom, $greenTo, $greenArc, IMG_ARC_PIE);
        }
        imagefilledellipse($im, $cX, $cY, $dXRingCenter, $dYRingCenter, $bgcolor);

        //ticks
        $radiusX = intval($dX * 0.42);
        $radiusY = intval($dY * 0.42);
        $min = 5;
        while ($min <= 55) {
            if ($min % 5 == 0) {
                $len = $radiusX / 8;
            } else {
                $len = $radiusX / 25;
            }

            $ang = (2 * M_PI * $min) / 60;
            $x1 = sin($ang) * ($radiusX - $len) + $cX;
            $y1 = cos($ang) * ($radiusY - $len) + $cY;
            $x2 = sin($ang) * $radiusX + $cX;
            $y2 = cos($ang) * $radiusY + $cY;

            ImageLine($im, $x1, $y1, $x2, $y2, $black);

            if ($min % 5 == 0) {
                $textToDisplay = sprintf("%d", (55 - $min) * $this->maxValue / 50);
                $bbox = imagettfbbox(8, 0, $fontArial, $textToDisplay);
                $x1 = sin($ang) * ($radiusX - 2.5 * $len) + $cX - $bbox[4] / 2;
                $y1 = cos($ang) * ($radiusY - 2.5 * $len) + $cY + 2; // - abs($bbox[5]);
                imagettftext($im, 8, 0, $x1, $y1, $gray, $fontArial, $textToDisplay);
            }
            $min++;
        }

        if (trim($this->centerLabel) != '') {
            $textToDisplay = trim($this->centerLabel);
            $bbox = imagettfbbox(8, 0, $fontArial, $textToDisplay);
            $x1 = $cX - $bbox[4] / 2;
            $y1 = $cY * 3 / 4 + abs($bbox[5]);
            imagettftext($im, 8, 0, $x1, $y1, $black, $fontArial, $textToDisplay);
        }

        imagettftext($im, 9, 0, $cX * 0.60, $cY * 1.8, $gray, $fontArial, $this->open);
        imagettftext($im, 9, 0, $cX * 1.40, $cY * 1.8, $gray, $fontArial, $this->completed);

        //drawing the arrow, simple way
        $radiusX = intval($dX * 0.35);
        $radiusY = intval($dY * 0.35);

        $ang = - M_PI / 6 + 2 * M_PI - (2 * M_PI * $this->value) * 50 / 60 / $this->maxValue;
        $x1 = sin($ang) * ($radiusX) + $cX;
        $y1 = cos($ang) * ($radiusY) + $cY;
        ImageLine($im, $cX, $cY, $x1, $y1, $arrowLine);

        /*
          //arrowLine
          $arrowHeight = intval($dY * 0.02);
          $arrowWidth  = intval($dX * 0.35);
          $arrowTail   = intval($dX * 0.15);
          $values = array(
          0, -$arrowHeight,
          -$arrowTail,  0,
          0, $arrowHeight,
          $arrowWidth, 0,
          0, -$arrowHeight
          );

          //rotate n degrees
          $n = 20;
          $ang = (2 * M_PI * $n) / 60;

          foreach ( $values as $k => $val ) {
          if ( $k % 2 == 0 ) {
          //$values[$k] = sin($ang)*$val + 20;
          $values[$k] = sin($ang)*($val/$cX)*$;
          $values[$k] += $cX;
          }
          else {
          //$ys = intval(sin($sec * M_PI/30 - M_PI/2) * R);
          //$values[$k] = intval(sin($n *  M_PI/30 - M_PI/2) *$val);
          $values[$k] = (cos($ang))*($val/$cY)*$cY;
          $values[$k] += $cY;
          }
          }

          imagefilledpolygon  ($im, $values, 5, $arrowBody);
          imagepolygon        ($im, $values, 5, $arrowLine);
         */
        //blue ring
        $dXBlueRing = $dX * 0.07;
        $dYBlueRing = $dY * 0.07;
        imagefilledellipse($im, $cX, $cY, $dXBlueRing, $dXBlueRing, $blueRing);
        imageellipse($im, $cX, $cY, $dXBlueRing, $dYBlueRing, $blueRingLine);

        imageellipse($im, $cX, $cY, $dX, $dY, $black);

        $textToDisplay = sprintf("%5.2f%%", $this->value);
        $bbox = imagettfbbox(9, 0, $fontArial, $textToDisplay);
        $centerX = $cX - $bbox[4] / 2;
        $centerY = $cY + $dYRing / 2 + 3 - abs($bbox[5]);
        imagettftext($im, 9, 0, $centerX, $centerY, $black, $fontArial, $textToDisplay);
    }
}
