<?php
$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("file")
		->label("Select file")
		->addClass('inputtext')
		->type('file')
		->attr('accept', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel')
		->value('');

$form 	->field("start_row")
		->label("Read the data in rows")
		->addClass('inputtext')
		->type('number')
		->value(1);

$form 	->field("tap")
		->label("The page you want to import (the bar below the 1st bar, enter 0)")
		->addClass('inputtext')
		->type('number')
		->value(0);

# set form
$arr['title'] = 'Import File xls, xlsx';

$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'orders/import" enctype="multipart/form-data"></form>';

# body
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);
