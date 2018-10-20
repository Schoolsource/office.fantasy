<?php 


$html = '';
$settings = array_merge(array(
	'title' => '',
	'type' => 'Legal', // A4, A4-L
	'mode' => 'fullpage', // real

), isset($this->pages) ? $this->pages: array() );

$file = WWW_VIEW."Themes/pdf/pages/sections/{$this->section}.php";


if (file_exists($file)) {

	include "sections/{$this->section}.php";

	// $html = file_get_contents('http://localhost/spa_arima/pdf/daily/summary?date=2017-08-23', true);
	// echo $html; die;
}

if( $settings['title'] == '' ){
	$settings['title'] = mb_substr( strip_tags($html), 0, 60, 'utf-8');
}
// echo $html; die;


$content = '<!doctype html><html lang="th"><head><title id="pageTitle">plate</title><meta charset="utf-8" /></head><body>'.$html.'</body></html>';


ob_clean();
header('Content-type: application/pdf; charset=utf-8');
header('Content-Disposition: inline; filename="' . $settings['title'] . '"');
header('Content-Transfer-Encoding: binary');
// header('Accept-Ranges: bytes');


$mpdf = new mPDF( '', $settings['type'], '', '', 20, 5, 5, 5,0,0); // left, R, TOp, bootm 


$mpdf->SetTitle( $settings['title'] );
// $mpdf->SetAuthor('monkey.d.chong@gmail.com'); // Author
// $mpdf->SetCreator('monkey.d.chong@gmail.com'); // My Creator
// $mpdf->SetSubject('My Subject');
// $mpdf->SetKeywords('My Keywords, More Keywords');

$mpdf->debug = true;
$mpdf->allow_charset_conversion = true;
// $mpdf->charset_in = 'iso-8859-4';
// $mpdf->useOnlyCoreFonts = false;
$mpdf->charset_in='UTF-8';

if( isset($settings['horizontal']) ){
	$mpdf->AddPage('L'); // Adds a new page in Landscape orientation
}

if( isset($useDefaultCSS2) ){
	$mpdf->useDefaultCSS2 = true;
}

$mpdf->SetDisplayMode( $settings['mode'] );

$mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
$stylesheet = file_get_contents(VIEW . 'Themes/print/assets/css/main.css');
$mpdf->WriteHTML($stylesheet, 1);

$mpdf->WriteHTML( $content );
$mpdf->Output();