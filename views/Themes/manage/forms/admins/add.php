<?php

$title = 'System administrator';
if( !empty($this->item) ){
	$arr['title'] = "Edit {$title}";
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=>$this->item['id']);
}
else{
	$arr['title'] = "Add {$title}";
}

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("name")
		->label("Name *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['name']) ? $this->item['name'] : '' );

$form 	->field("username")
		->label("Username *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['username']) ? $this->item['username'] : '' );

if( empty($this->item) ){
	$form 	->field("password")
			->label("Password *")
			->autocomplete('off')
			->addClass('inputtext')
			->type('password')
			->value( '' );
}

$form 	->field("email")
		->label("Email")
		->autocomplete('off')
		->addClass('inputtext')
		->type('email')
		->value( !empty($this->item['email']) ? $this->item['email'] : '' );
# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'admins/save"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);
