<?php

$li = '';
foreach ($this->results['lists'] as $key => $value) {

	$li .= '<li class="ui-list-item border-bottom mhs pas"><a class="anchor clearfix" href="'.URL.'mobile/orders/'.$value['id'].'">'.

			'<div class="avatar lfloat icon tac mrm"><i class="icon-cube"></i></div>'.
			'<div class="rfloat fwb">
				<button class="btn btn-small" style="background-image: -webkit-linear-gradient(top, '.$value['process']['color'].', '.$value['process']['color'].'); color:#fff">'.$value['process']['name'].'</button>
			</div>'.

			'<div class="content">'.
				'<div class="spacer"></div>'.
					'<div class="massages">'.
					'<div class="ui-score"></div>'.
					'<div class="title fwb"><i class="icon-cube"></i> '.$value['code'].'</div>'.
					'<div class="fwn">'.$value['user_name'].'</div>'.
					'<div class="fwn"><i class="icon-clock-o"></i> '.date("d/m/Y", strtotime($value['date'])).'</div>'.
					'<div class="fwb" style="color:red;"><i class="icon-money"></i> '.number_format($value['net_price'], 2).'</div>'.
				'</div>'.
			'</div>'.
			'</a></li>';
}

echo json_encode( array_merge($this->results, array(
	'$lis'=>$li,
)) );