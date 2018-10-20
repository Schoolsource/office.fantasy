<?php

$options = array(
    'url' => URL.'media/set',
    'data' => array(
        'album_name'=>'my', 
        'minimize'=> array(128,128),
        'has_quad'=> true,
     ),
    'autosize' => true,
    'show'=>'quad_url',
    'remove' => true
);

if( !empty($this->me['id']) ){
    $options['setdata_url'] = URL.'employees/setdata/'.$this->me['id'].'/emp_image_id/?has_image_remove';
}

$image_url = '';
$hasfile = false;
if( !empty($this->me['image_url']) ){
    $hasfile = true;
    $image_url = '<img class="img" src="'.$this->me['image_url'].'?rand='.rand(100, 1).'">';

    $options['remove_url'] = URL.'media/del/'.$this->me['image_id'];
    
}

$picture_box = '<div class="anchor"><div class="clearfix">'.

        '<div class="ProfileImageComponent lfloat size80 radius mrm is-upload'.($hasfile ? ' has-file':' has-empty').'" data-plugins="uploadProfile" data-options="'.$this->fn->stringify( $options ).'">'.
            '<div class="ProfileImageComponent_image">'.$image_url.'</div>'.
            '<div class="ProfileImageComponent_overlay"><i class="icon-camera"></i></div>'.
            '<div class="ProfileImageComponent_empty"><i class="icon-camera"></i></div>'.
            '<div class="ProfileImageComponent_uploader"><div class="loader-spin-wrap"><div class="loader-spin"></div></div></div>'.
            '<button type="button" class="ProfileImageComponent_remove"><i class="icon-remove"></i></button>'.
        '</div>'.
    '</div>'.

'</div>';

$form = new Form();
$form = $form->create()
		->url(URL."me/updated/basic?run=1")
		->addClass('js-submit-form form-insert')
		->method('post');

/* $form   ->field("image")
		->label($this->lang->translate('Avatar'))
        ->text( $picture_box ); */

/* $form   ->field("emp_first_name")
        ->label($this->lang->translate('Name'))
        ->text( $this->fn->q('form')->fullname( $this->me, array('field_first_name'=>'emp_') ) ); */
$form   ->field('name')
        ->label('ชื่อ')
        ->addClass('inputtext')
        ->autocomplete('off')
        ->value( !empty($this->me['name']) ? $this->me['name'] : '' );

$form  	->field("email")
		->label($this->lang->translate('Email'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->me['email']) ? $this->me['email']:''  );

/* $form  	->field("emp_phone_number")
		->label($this->lang->translate('Phone'))
		->addClass('inputtext')
		->autocomplete("off")
		->value( !empty($this->me['phone_number']) ? $this->me['phone_number']:''   );

$form   ->field("emp_line_id")
        ->label('LineID')
        ->addClass('inputtext')
        ->autocomplete("off")
        ->value( !empty($this->me['line_id']) ? $this->me['line_id']:'' );

$a = array();
$a[] = array('id'=>'light', 'name'=>'Light');
$a[] = array('id'=>'dark', 'name'=>'Dark');
$a[] = array('id'=>'blue', 'name'=>'Blue');
$a[] = array('id'=>'green', 'name'=>'Green');

$mode = '';
if( empty($this->me['mode']) ) $this->me['mode'] = 'word';
foreach ($a as $key => $value) {
    
    $check = $this->me['mode']==$value['id'] ? ' checked="1"':'';
    $mode .= '<li><label class="radio"><input type="radio" name="emp_mode" value="'.$value['id'].'"'.$check.' />'.$value['name'].'</label></li>';
}

$form   ->field("emp_mode")
        ->label($this->lang->translate('Themes'))
        ->text( '<ul>'.$mode.'</ul>' ); */

$form  	->submit()
		->addClass("btn-submit btn btn-blue")
		->value($this->lang->translate('Save'));

echo $form->html();