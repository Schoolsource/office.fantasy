<?php 

$arr['title'] = $this->order['code'].' '.$this->item['type_name'];

$arr['body'] = '<div>
					<center><img style="width:450px; height:auto;" src="'.$this->item['image_arr']['original_url'].'">
					</center>
				</div>';

$arr['width'] = 500;
$arr['is_close_bg'] = true;

echo json_encode($arr);