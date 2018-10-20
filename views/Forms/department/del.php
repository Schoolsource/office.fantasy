<?php

$arr['title'] = $this->lang->translate('Confirm').' '.$this->lang->translate('Delete');
if ( !empty($this->item['permit']['del']) ) {
	
	$arr['form'] = '<form class="js-submit-form" action="'.URL. 'employees/del_department/"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
	$arr['body'] = "{$this->lang->translate('You want to delete')} <span class=\"fwb\">\"{$this->item['name']}\"</span> {$this->lang->translate('or not')} ?";
	
	$arr['button'] = '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">ลบ</span></button>';
	$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';
}
else{

	$arr['body'] = "{$this->lang->translate('You can not delete')} <span class=\"fwb\">\"{$this->item['name']}\"</span>";	
	$arr['button'] = '<a href="#" class="btn btn-cancel" role="dialog-close"><span class="btn-text">ปิด</span></a>';
}


echo json_encode($arr);