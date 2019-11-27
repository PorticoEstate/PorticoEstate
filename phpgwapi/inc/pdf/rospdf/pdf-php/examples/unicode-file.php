<?php

error_reporting(E_ALL & ~E_NOTICE);
set_include_path('../src/'.PATH_SEPARATOR.get_include_path());

include 'Cezpdf.php';

class Creport extends Cezpdf
{
    public function __construct($p, $o)
    {
        parent::__construct($p, $o, 'none', array());
        $this->isUnicode = true;
        // always embed the font for the time being
        //$this->embedFont = false;
        // since version 0.11.8 it is required to allow custom callbacks
        $this->allowedTags .= '|uline';
    }
}
$pdf = new Creport('a4', 'portrait');

$start = microtime(true);

$pdf->ezSetMargins(20, 20, 20, 20);
//$pdf->rtl = true; // all text output to "right to left"
//$pdf->setPreferences('Direction','R2L'); // optional: set the preferences to "Right To Left"

$f = (isset($_GET['font'])) ? $_GET['font'] : 'FreeSerif';

$tmp = array(
    'b' => 'FreeSerifBold',
);
$pdf->setFontFamily('FreeSerif', $tmp);

$mainFont = $f;
// select a font
$pdf->selectFont($mainFont);
$pdf->openHere('Fit');

$content = file_get_contents('utf8.txt');

$pdf->ezText($content, 10, array('justification' => 'full'));

if (isset($_GET['d']) && $_GET['d']) {
    echo $pdf->ezOutput(true);
} else {
    $pdf->ezStream();
}

$end = microtime(true) - $start;
//error_log($end . ' execution in seconds (v0.12.2)');
;
