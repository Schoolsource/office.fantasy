<?php 

$html = '<table  style="margin-top:5mm;">
			<tbody>
				<tr>
					<th style="padding-right:1rem;" class="tar">'.$this->fn->q('time')->full(strtotime($this->results['date']), true,true, false).'</th>
				</tr>
			</tbody>
    </table>';
    
   $html .='
        <table  style="margin-top:7mm;">
            <tbody>
                <tr>
                    <th style="padding-left:9rem;" class="tal">'.$this->results['sup_name'].'</th>
                </tr>
            </tbody>
        </table>
   ';
   $html .='
   <table  style="margin-top:6mm;">
            <tbody>
                <tr>
                    <th style="padding-left:12rem;" class="tal">'.$this->fn->q('text')->convertBaht($this->results['price']).'</th>
                    <th></th>
                </tr>
            </tbody>
        </table>
   ';
   $html .='
   <table  style="margin-top:7mm;">
            <tbody>
                <tr>
                    <th style="padding-right:1rem;" class="tar">฿ '.number_format($this->results['price'],2).'</th>
                </tr>
            </tbody>
        </table>
   ';
$settings = array_merge(array(
	'title' => '',
	'format' => [195,92], // A4, A4-L
	'mode' => 'real', // real,

	'font_size' => '15px',
	'font' => 'thsarabun',

	'margin_left' => 20,
	'margin_right' => 5,
	'margin_top' => 5,
	'margin_bottom' => 5,
	'margin_header' => 0,
	'margin_footer' => 0,

	'output' => 'Modern Fantasy'

), isset($this->pages) ? $this->pages: array() );

$file = WWW_VIEW."Themes/pdf/pages/sections/{$this->section}.php";
if (file_exists($file)) {
	require_once $file;
	// echo $html; die;
}

$content = '<!doctype html><html lang="th">'.

'<head>'.
	'<title id="pageTitle">'.$settings['title'].'</title>'.

	'<meta charset="utf-8" />'.
	'<link rel="stylesheet" type="text/css" href="'.VIEW.'Themes/pdf/assets/css/main.css">'.
'<style>
.right{
    margin-left:150mm;
}
</style>'.
'</head>'.

'<body>'.$html.'</body></html>';
// echo $content; die;
// print_r($settings); die;

ob_clean();
header('Content-type: application/pdf; charset=utf-8');
header('Content-Disposition: inline; filename="' . $settings['title'] . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

// mode, format, font_size, font, 
// margin_left, margin_right, margin_top, margin_bottom, margin_header, margin_footer
$mpdf = new mPDF( 'th', 

	$settings['format'], 
	$settings['font_size'], 
	$settings['font'], 
	
	$settings['margin_left'], 
	$settings['margin_right'], 
	$settings['margin_top'], 
	$settings['margin_bottom'], 
	$settings['margin_header'], 
	$settings['margin_footer']
);


if( isset($settings['horizontal']) ){
	$mpdf->AddPage('L'); // Adds a new page in Landscape orientation 
}

if( isset($settings['title']) ){
	$mpdf->SetTitle($settings['title']);

	$settings['output'] .= !empty($settings['output']) ? ' - ':'';
	$settings['output'] .= $settings['title'];
}

$mpdf->debug = true;
$mpdf->allow_charset_conversion = true;
/*$mpdf->charset_in = 'iso-8859-4';
$mpdf->useOnlyCoreFonts = false;*/
$mpdf->charset_in='UTF-8';

$mpdf->SetDisplayMode( $settings['mode'] );

$mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
// $stylesheet = ;
// $mpdf->WriteHTML(file_get_contents(VIEW . 'Themes/pdf/assets/css/main.css'), 1);



$mpdf->WriteHTML( $content );
$mpdf->Output( $settings['output'], 'I' );