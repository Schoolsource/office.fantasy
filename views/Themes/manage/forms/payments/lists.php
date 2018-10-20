<?php

$arr['title'] = $this->item['code'];

$balance = 0;

$tr = '';
$no = 1;
foreach ($this->payment as $key => $value) {

		$type = "";

		if( !empty($value['type_is_cash']) ){
			$type = $value['type_name'];
		}
		elseif( !empty($value['type_is_bank']) ){
			$type = "<div>".$value['type_name'].'</div>';
			$type .= "<div>(".$value['bank_code'].") ".$value['account_number']."</div>";
		}
		elseif( !empty($value['type_is_check']) ){
			$type = "<div>".$value['type_name'].'</div>';
			$type .= "<div>".$value['bank_code']."-".$value['check_number']."</div>";
		}

		$image = '-';
		if( !empty($value['image_arr']) ){
			$image = '<a href="'.$value['image_arr']['original_url'].'" target="_blank" class=""><i class="icon-eye"></i></a>';
		}

		$tr .= '<tr data-id="'.$value['id'].'">'.
				'<td class="ID">'.$no.'</td>'.
				'<td class="date tac">'.date("d/m/Y", strtotime($value['date'])).'</td>'.
				'<td class="type tac">'.$type.'</td>'.
				'<td class="status tac">'.$image.'</td>'.
				'<td class="price tac">'.number_format($value['amount'], 2).'</td>'.
				// '<td class="actions tac"><a class="btn btn-red js-del"><i class="icon-trash"></i></a></td>'.
				/* '<td class="actions tac">'.
					'<a data-plugins="dialog" href="'.URL.'payments/del/'.$value['id'].'" class="btn btn-red"><i class="icon-trash"></i></a>';
				'</td>'. */
			'</tr>';
	$no++;
	$balance += $value['amount'];
}

$body = '<table width="100%" class="table-permit">'.
			'<thead>'.
				'<tr>'.
					'<th> No. </th>'.
					'<th>Date</td>'.
					'<th>Payment method</td>'.
					'<th>Evidence</td>'.
					'<th>Amount</td>'.
					// '<th></td>'.
				'<tr>'.
			'</thead>'.
			'<tbody>'.$tr.'</tbody>'.
			'<tfoot>
				<tr>
					<th colspan="4" class="tar"><span class="fwb">Total</span></td>
					<th><span class="total">'.number_format($balance, 2).'</span></th>
				</tr>
			</tfoot>'.
		 '</table>';

$arr['body'] = $body;
$arr['width'] = 600;
// $arr['height'] = 350;
$arr['is_close_bg'] = 1;


// $arr['bottom_msg'] .= '<a class="btn" role="dialog-close"><span class="btn-text">ปิด</span></a>';

// $arr['button'] .= '<a class="btn btn-green" href="'.URL.'events/'.$this->type.'/'.$this->data['id'].'" data-plugins="dialog"><i class="icon-pencil mrs"></i><span class="btn-text">แก้ไข</span></a>';

echo json_encode($arr);
?>
