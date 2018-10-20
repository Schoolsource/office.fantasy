<?php 

$arr['title'] = $this->item['number'].' ('.$this->item['bank_name'].'-'.$this->item['bank_code'].')';

$arr['body'] = '<div>
					<center><img style="width:800px; height:auto;" src="'.$this->item['image_arr']['original_url'].'">
					</center>
				</div>';

$arr['width'] = 850;
$arr['is_close_bg'] = true;

echo json_encode($arr);