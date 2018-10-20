<?php

$arr['title'] = 'Confirm deletion';

$next = isset($_REQUEST['next']) ? '?next='.$_REQUEST['next']:'';

if( !empty($this->item['permit']['del']) ){
	
	$arr['form'] = '<form class="js-submit-form" action="'.URL.'customers/deleteProject'.$next.'"></form>';
	$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['project_id']);
	$arr['body'] = "You want to delete project: <span class=\"fwb\">\"{$this->item['project_name']}\"</span>?";
	
	$arr['button'] = '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">Delete</span></button>';
	$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';
	$arr['bg'] = 'red';
}
else{

	$arr['body'] = "You can't delete project:<span class=\"fwb\">\"{$this->item['project_name']}\"</span>";	
	$arr['button'] = '<a class="btn" role="dialog-close"><span class="btn-text">Close</span></a>';
	$arr['bg'] = 'red';
}


echo json_encode($arr);