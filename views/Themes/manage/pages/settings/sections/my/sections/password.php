<?php

$form = new Form();
$form = $form->create()
		->url(URL."me/updated/password?run=1")
		->addClass('js-submit-form form-insert')
		->method('post');

$form->field("password_old")
        ->label($this->lang->translate('Password'))
        ->type('password')
        ->addClass('inputtext')
        ->maxlength(30)
        ->required(true)
        ->autocomplete("off")

    ->field("password_new")
        ->label($this->lang->translate('New Password'))
        ->type('password')
        ->addClass('inputtext')
        ->maxlength(30)
        ->required(true)
        ->autocomplete("off")

    ->field("password_confirm")
        ->label($this->lang->translate('Confirm Password'))
        ->type('password')
        ->addClass('inputtext')
        ->maxlength(30)
        ->required(true)
        ->autocomplete("off");

$form  	->submit()
		->addClass("btn-submit btn btn-blue")
		->value("Save");
        
echo $form->html();