<?php

$startDay = date('d', strtotime($this->start));
$endDay = date('d', strtotime($this->end));

$startMonth = date('n', strtotime($this->start));
$endMonth = date('n', strtotime($this->end));
$startMonthStr = $this->fn->q('time')->month($startMonth);
$endMonthStr = $this->fn->q('time')->month($endMonth);

$startYear = date('Y', strtotime($this->start)) + 543;
$endYear = date('Y', strtotime($this->end)) + 543;

if ($startDay == $endDay) {
    $periodStr = "{$startDay} {$startMonthStr} {$startYear}";
} else {
    if ($startMonth == $endMonth) {
        $startMonthStr = '';
    }
    if ($startYear == $endYear) {
        $startYear = '';
    }

    $periodStr = "{$startDay} {$startMonthStr} {$startYear} - {$endDay} {$endMonthStr} {$endYear}";
}

$title = 'รายรับประจำวัน';
$html = '';

$settings['title'] = $title.' วันที่ '.$periodStr;

$html .= '<table>
		<tbody>
			<tr>
				<th class="tal">รายงาน</th>
				<th class="tar"> ประจำวันที่ '.$periodStr.'</th>
			</tr>
		</tbody>
	</table>';

$lists = '';
$total = 0;
foreach ($this->results['lists'] as $key => $value) {
    $lists .= '<tr><td class="price">'.$value['code'].'</td>
			  <td class="price">'.$value['user_code'].'</td>
			  <td class="tal" style="text-align:left;">'.$value['user_name'].'</td>
			  <td class="price">'.$value['term_of_payment']['name'].'</td>
			  <td class="price">'.$value['sale_name'].'</td>
			  <td class="price tar">'.number_format($value['net_price'], 2).'</td></tr>';

    $total += $value['net_price'];
}

$html .= '<table class="tRep-masIncome" style="margin-top:5mm;">
			<thead>
				<tr>
					<th class="price" width="15%">เลขที่ใบสั่งซื้อ</th>
					<th class="price" width="10%">รหัสลูกค้า</th>
					<th class="price" width="30%">ชื่อลูกค้า</th>
					<th class="price" width="15%">Term</th>
					<th class="price" width="15%">ชื่อเซลล์</th>
					<th class="price" width="15%">ราคา</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="tar" colspan="5">TOTAL</th>
					<th class="tac">'.number_format($total, 2).'</th>
				</tr>
			</tfoot>
			<tbody>
				'.$lists.'
			</tbody>
	</table>';
