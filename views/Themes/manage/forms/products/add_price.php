<?php

$arr['title'] = $this->item['pds_name'];
if( !empty($this->item) ){
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=>$this->item['id']);
}

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("frontend")
		->label("Member Price *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['frontend']) ? $this->item['frontend'] : '' );

$form 	->field("website")
		->label("Website Price *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['website']) ? $this->item['website'] : '' );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'products/set_price"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);
