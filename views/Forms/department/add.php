<?php

# title
$title = $this->lang->translate('Department');
if( !empty($this->item) ){
    $arr['title']= $title;
    $arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
}
else{
    $arr['title']= $title;
}


$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

// ประเภท
$form 	->field("dep_name")
    	->label($this->lang->translate('Name').'*')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['name'])? $this->item['name']:'' );

$form   ->field("dep_notes")
        ->label($this->lang->translate('Note'))
        ->type('textarea')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->attr('data-plugins', 'autosize')
        ->placeholder('')
        ->value( !empty($this->item['notes'])? $this->fn->q('text')->textarea($this->item['notes']):'' );

$role = '';
foreach ($this->role as $key => $value) {

    $ck = '';
    if( !empty($this->item['access']) ){
        if( in_array($value['id'], $this->item['access']) ){
            $ck = ' checked="1"';
        }
    }

    $role .= '<label class="checkbox mrl"><input'.$ck.' type="checkbox" name="access[]" value="'.$value['id'].'"><span class="fwb">'.$value['name'].'</span></label>';
}

$form   ->field("dep_access")
        ->text( $role );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'employees/save_department"></form>';

# body
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);