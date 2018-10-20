<?php 

$arr['title'] = 'บิลที่ค้างเก็บ';
$tbody = '';
$total_net_price = 0;
$total_balance = 0;
foreach ($this->results as $key => $value) {
	$total_net_price += $value['ord_net_price'];
	$total_balance += $value["balance"];
	$tbody .= '<tr>
					<td class="tac">'.date("d/m/y", strtotime($value['ord_dateCreate'])).'</td>
					<td class="tac">'.$value['ord_code'].'</td>
					<td class="fwb"><span class="mls">'.$value['user_name'].'</span></td>
					<td class="tac fwb" style="color:blue;">'.number_format($value['ord_net_price']).'</td>
					<td class="tac fwb" style="color:red;">'.number_format($value['balance']).'</td>
			   </tr>';
}

$arr['body'] = '<div class="">
					<table class="table-bordered" width="100%">
						<thead>
							<tr>
								<th width="15%">วันที่</th>
								<th width="15%">บิล</th>
								<th width="40%">ชื่อร้าน</th>
								<th width="15%">ยอดขาย</th>
								<th width="15%">ยอดค้าง</th>
							</tr>
						</thead>
						<tbody>
							'.$tbody.'
						</tbody>
						<tfoot>
							<th colspan="3" class="fwb tar">รวม</th>
							<th class="fwb tac">'.number_format($total_net_price).'</th>
							<th class="fwb tac">'.number_format($total_balance).'</th>
						</tfoot>
					</table>
			 	</div>';

// $arr['height'] = 'full';
$arr['width'] = 1024;
$arr['is_close_bg'] = true;

echo json_encode($arr);