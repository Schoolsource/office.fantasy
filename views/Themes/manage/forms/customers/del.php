<?php

$arr['title'] = "Confirm deletion";
if ( !empty($this->item['permit']['del']) ) {

	$arr['form'] = '<form class="js-submit-form" action="'.URL. 'customers/del"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
	if( isset($_REQUEST["next"]) ) {
		$arr['hiddenInput'][] = array('name'=>'next', 'value'=>$_REQUEST["next"]);
	}
	$arr['body'] = "You want to delete <span class=\"fwb\">\"{$this->item['name_store']}\"</span> ?";

	$arr['button'] = '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">'.$this->lang->translate('Delete').'</span></button>';
	$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';
}
else{

	$arr['body'] = "You can not delete <span class=\"fwb\">\"{$this->item['name_store']}\"</span>";
	$arr['button'] = '<a href="#" class="btn btn-cancel" role="dialog-close"><span class="btn-text">Close</span></a>';
}


echo json_encode($arr);
