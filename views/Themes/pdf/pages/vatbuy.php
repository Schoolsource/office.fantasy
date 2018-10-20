<?php 

$tr = '';
foreach ($this->results["lists"] as $key => $value) {
	$tr .= '<tr>
				<td>'.$value["slipt"].'</td>
				<td align="center">'.date("d/m/Y", strtotime($value["date"])).'</td>
				<td>'.$value["sup_name"].'</td>
				<td align="right">'.number_format($value["total"], 2).'</td>
				<td align="right">'.number_format($value["vat"], 2).'</td>
			</tr>';
}

$head = '<style>
			@page{ margin : 10px 10px 10px 10px }
			table, th, td {
   				border: 1px solid #000;
   				border-collapse:collapse;
			}
		</style>';
$html .= '<div>
			<h3 style="text-align:center;">รายงาน Vat ซื้อ <br/>'.$this->fn->q('time')->str_event_date($_REQUEST["period_start"], $_REQUEST["period_end"]).' '.( date("Y", strtotime($_REQUEST["period_end"])) +543).'</h3>
		</div>
		<div>
		<table width="100%">
			<thead>
				<tr style="background-color:Violet;">
					<th width="15%" style="font-weight: bold;">Slipt</th>
					<th width="10%" style="font-weight: bold;">Date</th>
					<th width="30%" style="font-weight: bold;">Name</th>
					<th width="10%" style="font-weight: bold;">Total</th>
					<th width="10%" style="font-weight: bold;">Vat</th>
				</tr>
			</thead>
			<tbody>';

$total_list = 0;
$total = 0;
$vat = 0;

if( $this->results["total"] > 45 ) $total_list = $this->results["total"];
foreach ($this->results["lists"] as $key => $value) {
	$html .= '<tr>
				<td>'.$value["slipt"].'</td>
				<td align="center">'.date("d/m/Y", strtotime($value["date"])).'</td>
				<td style="padding-left:2mm;">'.$value["sup_name"].'</td>
				<td style="padding-right:1mm;" align="right">'.number_format($value["total"], 2).'</td>
				<td style="padding-right:1mm;" align="right">'.number_format($value["vat"], 2).'</td>
			</tr>';
	$total_list--;
	$total += $value["total"];
	$vat += $value["vat"];
}
$html .=	'</tbody>';

if( $total_list < 45 ){
$html .= '<tfoor>
			<tr style="background-color:Violet;">
				<th colspan="3">รวม</th>
				<th align="right">'.number_format($total, 2).'</th>
				<th align="right">'.number_format($vat, 2).'</th>
			</tr>		
		  </tfoot>';
}

$html .=	'</table>
		</div>';

$content = '<!doctype html><html lang="th"><head><title id="pageTitle">plate</title><meta charset="utf-8" />'.$head.'</head><body>'.$html.'</body></html>';

// echo $content;

$mpdf = new mPDF('th', 'A4', '0');

$mpdf->debug = true;
$mpdf->allow_output_buffering = true;

$mpdf->charset_in='UTF-8';
$mpdf->allow_charset_conversion = true;

$mpdf->list_indent_first_level = 0;

// $stylesheet = file_get_contents(CSS . 'bootstrap.css');
// $mpdf->WriteHTML($stylesheet,1);

// $stylesheet2 = file_get_contents(VIEW.'Themes/plate/assess/css/main.css');
// $mpdf->WriteHTML($stylesheet2,1);

// $content = iconv('UTF-8', 'windows-1252', $content);
// $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

ob_clean();
$mpdf->SetTitle('VAT BUY M-'.$this->month.' Y-'.$this->year);
$mpdf->WriteHTML( $content );
$mpdf->Output('vat_buy.pdf','I');
