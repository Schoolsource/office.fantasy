<?php

$arr['title'] = 'Confirm delete of Data.';

$arr['form'] = '<form class="js-submit-form" action="'.URL.'customers/dels"></form>';
foreach ($this->ids as $key => $id) {
	$arr['hiddenInput'][] = array('name'=>'ids[]','value'=>$id);
}
if( isset($_REQUEST['next']) ){
	$arr['hiddenInput'][] = array('name'=>'next','value'=>$_REQUEST['next']);
}

$submit = '';
if( isset($_REQUEST['callback']) ){
	$arr['hiddenInput'][] = array('name'=>'callback','value'=>$_REQUEST['callback']);
	$submit = ' role="submit"';
}

$arr['body'] = "You want to delete Customers?";


$arr['button'] = '<button'.$submit.' type="submit" class="btn btn-danger btn-submit"><span class="btn-text">'.$this->lang->translate('Delete').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

echo json_encode($arr);