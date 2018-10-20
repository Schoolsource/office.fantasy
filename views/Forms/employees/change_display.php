<?php

$arr['title'] = $this->lang->translate('Confirm');
	
$arr['form'] = '<form class="js-submit-form" action="'.URL.'employees/display"></form>';
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
$arr['hiddenInput'][] = array('name'=>'status','value'=>$this->status);


$this->status = ucfirst($this->status);
$arr['body'] = "{$this->lang->translate('Change Status')}<span class=\"fwb\">{$this->item['fullname']}</span> to <span class=\"fwb fcr\">{$this->status}</span>";

$arr['button'] = '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);