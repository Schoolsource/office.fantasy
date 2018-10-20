<?php

$title = $this->lang->translate('Position');

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

// ประเภท
$department = '<option value="0">-</option>';
foreach ($this->department as $key => $value) {
    
    $selected = '';
    if( !empty($this->item['dep_id']) ){
        if( $this->item['dep_id']==$value['id'] ){
            $selected = ' selected="1"';
        }
    }

    $department .= '<option'.$selected.' value="'.$value['id'].'">'.$value['name'].'</option>';
}
$department = '<select class="inputtext" name="pos_dep_id">'.$department.'</select>';
$form   ->field("pos_dep_id")
        ->label($this->lang->translate('Department'))
        ->text( $department );

$form 	->field("pos_name")
    	->label($this->lang->translate('Name').'*')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['name'])? $this->item['name']:'' );

$form->field("pos_notes")
    ->label($this->lang->translate('Note'))
    ->type('textarea')
    ->autocomplete('off')
    ->addClass('inputtext')
    ->attr('data-plugins', 'autosize')
    ->placeholder('')
    ->value( !empty($this->item['notes'])? $this->fn->q('text')->textarea($this->item['notes']):'' );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'employees/save_position"></form>';

# body
$arr['body'] = $form->html();

# title
if( !empty($this->item) ){
    $arr['title']= "แก้ไข{$title}";
    $arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
}
else{
    $arr['title']= "เพิ่ม{$title}";
}

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

// $arr['width'] = 782;

echo json_encode($arr);