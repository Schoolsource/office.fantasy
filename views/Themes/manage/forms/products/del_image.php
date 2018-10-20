<?php

$arr['title'] = 'Confirm ?';
$arr['body'] = 'You want delete this Photo ?';
$arr['form'] = '<form class="js-submit-form" action="'.URL.'products/del_image"></form>';
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
$arr['button'] = '<button type="submit" class="btn btn-danger btn-submit"><span class="btn-text">'.$this->lang->translate('Delete').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

echo json_encode($arr);