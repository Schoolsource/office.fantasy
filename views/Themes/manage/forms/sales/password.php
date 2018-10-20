<?php

$title = 'Seller password';
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


    $form 	->field("password_1")
    ->label("Password :")
    ->autocomplete('off')
    ->type('password')
    ->addClass('inputtext');

    $form 	->field("password_2")
    ->label("Confirm Password :")
    ->autocomplete('off')
    ->type('password')
    ->addClass('inputtext');

	// ->value( !empty($this->item['password']) ? $this->item['password'] : '' );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'sales/change_password"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);
