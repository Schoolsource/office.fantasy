<?php

$title = 'Staff Information';
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

	$form 	->field("username")
			->label("Username * :")
			->autocomplete('off')
			->addClass('inputtext')
			->value( !empty($this->item['username']) ? $this->item['username'] : '' );

	if (empty($this->item)) {

		$form 	->field("password")
				->label("Password * :")
				->autocomplete('off')
				->type('password')
				->addClass('inputtext');
	}

	$form 	->field("sale_code")
			->label("Seller ID * :")
			->autocomplete('off')
			->addClass('inputtext')
			->value( !empty($this->item['sale_code']) ? $this->item['sale_code'] : '' );

	$form 	->field("sale_name")
			->label("Seller Name * :")
			->autocomplete('off')
			->addClass('inputtext')
			->value( !empty($this->item['sale_name']) ? $this->item['sale_name'] : '' );

	$form 	->field("sale_fullname")
			->label("Full name seller * :")
			->autocomplete('off')
			->addClass('inputtext')
			->value( !empty($this->item['sale_fullname']) ? $this->item['sale_fullname'] : '' );

	$form 	->field("region")
			->label("Region * :")
			->autocomplete('off')
			->addClass('inputtext')
			->select($this->region)
			->value( !empty($this->item['region']) ? $this->item['region'] : '' );

	$form 	->field("status")
			->label("Status * :")
			->autocomplete('off')
			->addClass('inputtext')
			->select($this->status)
			->value( !empty($this->item['status']) ? $this->item['status'] : '' );

	$form   ->field("department")
			->label("Department * :")
			->autocomplete('off')
			->addClass('inputtext')
			->select($this->department)
			->value( !empty($this->item['department']) ? $this->item['department'] : '' ); 

	// ->value( !empty($this->item['password']) ? $this->item['password'] : '' );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'sales/save"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);
