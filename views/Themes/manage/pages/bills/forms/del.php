<?php

$arr['title'] = 'Confirm ?';

$next = isset($_REQUEST['next']) ? '?next='.$_REQUEST['next']:'';

if( $this->item['permit']['del'] ){
	
	$arr['form'] = '<form class="js-submit-form" action="'.URL.'bills/del'.$next.'"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
	$arr['body'] = "{$this->lang->translate('You want to delete')}<span class=\"fwb\">\"{$this->item['number']}\"</span> {$this->lang->translate('or not')}?";
	
	$arr['button'] = '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">Delete</span></button>';
	$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';
}
else{

	$arr['body'] = "{$this->lang->translate('You can not delete')} <span class=\"fwb\">\"{$this->item['number']}\"</span>";	
	$arr['button'] = '<a href="#" class="btn btn-cancel" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Close').'</span></a>';
}


echo json_encode($arr);