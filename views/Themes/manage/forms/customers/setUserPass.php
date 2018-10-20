<?php

$arr['title'] = "Confirm settings Username & Password";

$arr['form'] = '<form class="js-submit-form" action="'.URL. 'customers/set_userpass"></form>';
$arr['hiddenInput'][] = array('name'=>'submit', 'value'=>'1');
$arr['body'] = "Do you want to <span class=\"fwb\">\"Username and password settings are not available to users who are unable to login.\"</span> ?";

$arr['button'] = '<button type="submit" class="btn btn-orange btn-submit"><span class="btn-text">'.$this->lang->translate('SET').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';


echo json_encode($arr);
