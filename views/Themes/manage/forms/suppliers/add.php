<?php

$title = 'Supplier';
if( !empty($this->item) ){
	$arr['title'] = "Edit {$title}";
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=>$this->item['id']);
}
else{
	$arr['title'] = "Add {$title}";
}

$arr['hiddenInput'][] = array('name'=>'sup_type_id', 'value'=>1);

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("sup_code")
		->label("CODE")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['code']) ? $this->item['code'] : '' );

/* $form 	->field("sup_type_id")
		->label("ประเภท")
		->autocomplete('off')
		->addClass('inputtext')
		->select($this->type)
		->value( !empty($this->item['type_id']) ? $this->item['type_id'] : 1 ); */

$form 	->field("sup_name")
		->label("Supplier Name*")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['name']) ? $this->item['name'] : '' );

$form   ->field("name")
        ->label('Contact Name*')
        ->text( $this->fn->q('form')->fullname( !empty($this->item)?$this->item:array(), array('field_first_name'=>'sup_', 'prefix_name'=>$this->prefixName) ) );

/* $form   ->field("sup_address")
        ->name('sup[address]')
        ->label('ที่อยู่ / Address*')
        ->text( $this->fn->q('form')->address( !empty($this->item['address'])? $this->item['address']:array(), array('city'=>$this->city ) ) ); */

$form 	->field("sup_address")
		->label("Address*")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['address']) ? $this->item['address'] : '' );

$form 	->field("sup_street")
		->label("Road")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['street']) ? $this->item['street'] : '' );

$form 	->field("sup_supdistrict")
		->label("District*")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['supdistrict']) ? $this->item['supdistrict'] : '' );

$form 	->field("sup_district")
		->label("Area*")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['district']) ? $this->item['district'] : '' );

$form 	->field("sup_province_id")
		->label("Province")
		->autocomplete('off')
		->addClass('inputtext')
		->select( $this->city )
		->value( !empty($this->item['province_id']) ? $this->item['province_id'] : '' );

$form 	->field("sup_zip")
		->label("Zip code*")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['zip']) ? $this->item['zip'] : '' );

$form 	->field("sup_country_id")
		->label("Country*")
		->autocomplete('off')
		->addClass('inputtext')
		->select( $this->country )
		->value( !empty($this->item['country_id']) ? $this->item['country_id'] : '' );

$form 	->field("sup_mobile_phone")
		->label("Mobile Phone")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['mobile_phone']) ? $this->item['mobile_phone'] : '' );

$form 	->field("sup_phone")
		->label("Phone")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['phone']) ? $this->item['phone'] : '' );

$form 	->field("sup_fax")
		->label("FAX")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['fax']) ? $this->item['fax'] : '' );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'suppliers/save"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['width'] = 550;
$arr['is_close_bg'] = true;

echo json_encode($arr);
