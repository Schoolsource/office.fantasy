<?php

$form = new Form();
$form = $form->create()
		->url(URL."settings/company?run=1")
		->addClass('js-submit-form form-insert')
		->method('post');

$form  	->field("name")
		->label($this->lang->translate('Company Name'))
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")
		->value( !empty($this->system['name']) ? $this->system['name']:'' );

$form  	->field("title")
		->label($this->lang->translate('Company Name (English)'))
		->addClass('inputtext')
		->required(true)
		->autocomplete("off")
		->value( !empty($this->system['title']) ? $this->system['title']:'' );

$form  	->field("address")
		->label($this->lang->translate('Address'))
		->type('textarea')
		->addClass('inputtext')
		->autocomplete("off")
		->attr('data-plugins', 'autosize')
		->value( !empty($this->system['address']) ? $this->system['address']:'');

$form  	->field("phone")
		->label($this->lang->translate('TEL'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['phone']) ? $this->system['phone']:'');

$form  	->field("phone_2")
		->label($this->lang->translate('TEL'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['phone_2']) ? $this->system['phone_2']:'');

$form  	->field("mobile_phone")
		->label($this->lang->translate('Mobile Phone บัญชี'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['mobile_phone']) ? $this->system['mobile_phone']:'');

$form  	->field("mobile_phone_2")
		->label($this->lang->translate('Mobile Phone ลูกค้าสัมพันธ์'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['mobile_phone_2']) ? $this->system['mobile_phone_2']:'');

$form  	->field("mobile_phone_3")
		->label($this->lang->translate('Mobile Phone ช่าง'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['mobile_phone_3']) ? $this->system['mobile_phone_3']:'');

// $form  	->field("fax")
// 		->label($this->lang->translate('Fax'))
// 		->addClass('inputtext')
// 		->autocomplete("off")
// 		->value( !empty($this->system['fax']) ? $this->system['fax']:'');

// $form  	->field("license")
// 		->label($this->lang->translate('License No.'))
// 		->addClass('inputtext')
// 		->autocomplete("off")
// 		->value( !empty($this->system['license']) ? $this->system['license']:'');

$form  	->field("email")
		->label($this->lang->translate('Email'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['email']) ? $this->system['email']:'');

$form  	->field("line_1")
		->label("Line บัญชี")
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['line_1']) ? $this->system['line_1']:'');

$form  	->field("line_2")
		->label("Line ลูกค้าสัมพันธ์")
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['line_2']) ? $this->system['line_2']:'');

$form  	->field("line_3")
		->label("Line ช่าง")
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['line_3']) ? $this->system['line_3']:'');

$form  	->field("facebook")
		->label($this->lang->translate('Facebook ID'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->system['facebook']) ? $this->system['facebook']:'');

$form  	->submit()
		->addClass("btn-submit btn btn-blue")
		->value($this->lang->translate('Save'));

echo $form->html();
?>
