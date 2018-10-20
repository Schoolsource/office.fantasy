<?php

$arr['title'] = $this->lang->translate('Confirm');
$arr['body'] = $this->lang->translate('You want to delete avatar');
$arr['form'] = '<form action="'.URL.'employees/del_image_profile"></form>';
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->id);
$arr['button'] = '<a href="#" class="btn btn-link btn-cancel" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';
$arr['button'] .= '<button type="submit" role="submit" class="btn btn-submit btn-red"><span class="btn-text">'.$this->lang->translate('Delete').'</span></button>';

echo json_encode($arr);