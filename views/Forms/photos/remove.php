<?php

$arr['title'] = 'ยืนยันการลบรูป';
if( !empty($this->item['permit']['del']) ){
	
	$arr['form'] = '<form class="js-submit-form" action="'.URL. 'photos/remove"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
	$arr['body'] = "คุณต้องการลบรูปนี้หรือไม่?";
	
	$arr['button'] = isset($_REQUEST['callback'])
		? '<button type="submit" role="submit" class="btn btn-danger btn-submit"><span class="btn-text">ลบ</span></button>'
		: '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">ลบ</span></button>';

	$arr['bottom_msg'] = '<a class="btn" role="close"><span class="btn-text">ยกเลิก</span></a>';
}
else{

	$arr['body'] = "คุณไม่สามารถลบรูปนี้ได้?";	
	$arr['button'] = '<a href="#" class="btn btn-cancel" role="close"><span class="btn-text">ปิด</span></a>';
}


if( isset($_REQUEST['next']) ){
	$arr['hiddenInput'][] = array('name'=>'next','value'=>$_REQUEST['next']);
}
$arr['bg'] = 'red';
echo json_encode($arr);