<?php 

$term_of_payment = $_REQUEST['term_of_payment'];
if($term_of_payment == 1) {
	$term = '(เงินสด)';
}elseif ($term_of_payment == 2) {
	$term = '(เครดิต 30 วัน)';
}elseif ($term_of_payment == 3) {
	$term = '(บัตรเครดิต)';
}elseif ($term_of_payment == 4) {
	$term = '(Cheque)';
}else {
	$term = '(โอน)';
}

$head = '<style>
			@page{ margin : 30px 10px 30px 10px }
			table, th, td {
   				border: 1px solid #000;
   				border-collapse:collapse;
			}
			th, td{
				padding-top:5px;
			}
		</style>';

$html = '<div>
			<h3 style="margin-top:20px;"><I>บริษัท โมเดิร์น แฟนตาซี จำกัด</I></h3>
			<div class="clearfix">
				<label style="float:left; margin-right:20px;" class="fwb"><U style="font-weight:bold;">รายงานภาษีขายประจำวันที่</U> '.$this->fn->q('time')->str_event_date($_REQUEST["period_start"], $_REQUEST["period_end"]).' '.( date("Y", strtotime($_REQUEST["period_end"])) +543).' '.$term.'
				</label>
				<label style="float:right;"><span style="font-weight:bold;">เลขประจำตัวผู้เสียภาษี</span> 0-1055-53049-89-9
				</label>
			</div>
		</div>
		<div>
			<table width="100%">
				<thead>
					<tr style="background-color:Violet;">
						<th width="10%">เลขที่ใบส่งของ</th>
						<th width="10%">วันที่ส่งของ</th>
						<th width="10%">รหัส</th>
						<th width="20%">ชื่อร้าน</th>
						<th width="15%">ยอดรวม</th>
						<th width="15%">VAT</th>
						<th width="15%">ยอดสุทธิ</th>
					</tr>
				</thead>
				<tbody>';

// $total_list = 0;
$total = 0;
$vat = 0;
$amount = 0;
$total_list = $this->results["total"];

foreach ($this->results['lists'] as $key => $value) {
	$html .= '<tr>
				<td align="center">IN'.sprintf("%05d", $value["id"]).'</td>
				<td align="center">'.date("d/m/Y", strtotime($value["send_date"])).'</td>
				<td align="center">'.$value["sub_code"].'</td>
				<td style="padding-left:1mm;">'.$value["name_store"].'</td>
				<td align="right">'.number_format($value["total"], 2).'</td>
				<td align="right">'.number_format($value["vat"], 2).'</td>
				<td align="right">'.number_format($value["amount"], 2).'</td>
			</tr>';

	$total_list--;
	$total += $value["total"];
	$vat += $value["vat"];
	$amount += $value["amount"];
}
$html .=	'</tbody>';

if( $total_list < 40 ){
// $html .= 	'<tfoor>
// 				<tr style="background-color:Violet;">
// 					<th colspan="4">รวม</th>
// 					<th align="right">'.number_format($total, 2).'</th>
// 					<th align="right">'.number_format($vat, 2).'</th>
// 					<th align="right">'.number_format($amount, 2).'</th>
// 				</tr>		
// 		  	</tfoot>';
$html .= '</table>';

$html .= '<div style="margin-top:10px;">
			<table width="100%">
				<tr>
					<td width="16.6%" align="center" style="font-weight:bold;">ยอดก่อนรวม VAT</td>
					<td width="16.6%" align="right">'.number_format($total, 2).'</td>
					<td width="16.6%" align="center" style="font-weight:bold;">VAT</td>
					<td width="16.6%" align="right">'.number_format($vat, 2).'</td>
					<td width="16.6%" align="center" style="font-weight:bold;">ยอดรวมทั้งสิ้น</td>
					<td width="16.6%" align="right">'.number_format($amount, 2).'</td>
				</tr>
			</table>
		  </div>';
}

$html .= '</div>';

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
$mpdf->SetTitle('VAT SALE M-'.$this->month.' Y-'.$this->year);
$mpdf->WriteHTML( $content );
$mpdf->setFooter('{PAGENO}');
$mpdf->Output('vat_sale.pdf','I');