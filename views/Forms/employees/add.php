<?php

$title = $this->lang->translate('Employees');

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

if( !empty($this->item['id']) ){
    $options['setdata_url'] = URL.'employees/setdata/'.$this->item['id'].'/emp_image_id/?has_image_remove';
}

$image_url = '';
$hasfile = false;
if( !empty($this->item['image_url']) ){
    $hasfile = true;
    $image_url = '<img class="img" src="'.$this->item['image_url'].'?rand='.rand(100, 1).'">';

    $options['remove_url'] = URL.'media/del/'.$this->item['image_id'];
    
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
	// set From
	->elem('div')
	->addClass('form-insert form-emp');

$form   ->field("image")
        ->text( $picture_box );

/*$form   ->field("emp_code")
        ->label($this->lang->translate('Number').'*')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->value( !empty($this->item['code'])? $this->item['code']:'' );*/

$form   ->field("emp_username")
        ->label($this->lang->translate('Username').'*')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->value( !empty($this->item['username'])? $this->item['username']:'' );
        
if( empty($this->item) ){

    $form   ->field("emp_password")
            ->label($this->lang->translate('Password').'*')
            ->type('password')
            ->maxlength(30)
            ->autocomplete('off')
            ->addClass('inputtext');
}

$form   ->field("name")
        ->label($this->lang->translate('Name'))
        ->text( $this->fn->q('form')->fullname( !empty($this->item)?$this->item:array(), array('field_first_name'=>'emp_', 'prefix_name'=>$this->prefixName) ) );


$form   ->field("emp_dep_id")
        ->label($this->lang->translate('Department'))
        ->value( !empty($this->item['dep_id']) ? $this->item['dep_id']:'')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->select( $this->department );

$form   ->field("emp_pos_id")
        ->label($this->lang->translate('Position'))
        ->autocomplete('off')
        ->addClass('inputtext')
        ->value( !empty($this->item['pos_id']) ? $this->item['pos_id']:'')
        ->select( $this->position );

$form   ->field("emp_address")
        ->label($this->lang->translate('Address'))
        ->type('textarea')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->attr('data-plugins', 'autosize')
        ->placeholder('')
        ->value( '' );
        // !empty($this->item['address'])? $this->fn->q('text')->textarea($this->item['address']):''

$birthday = array();
if( !empty($this->item['birthday']) ){
    if( $this->item['birthday'] != '0000-00-00' ){
        $birthday = $this->item;
    }
}

$form   ->field("birthday")
        ->label($this->lang->translate('Birthday'))
        ->text( $this->fn->q('form')->birthday( $birthday, array('field_first_name'=>'birthday') ) );

$form   ->field("emp_phone_number")
        ->label($this->lang->translate('Phone').'*')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['phone_number'])? $this->item['phone_number']:'' );

$form   ->field("emp_email")
        ->label($this->lang->translate('Email'))
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['email'])? $this->item['email']:'' );

$form   ->field("emp_line_id")
        ->label('Line ID')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['line_id'])? $this->item['line_id']:'' );

$form   ->field("emp_bio")
        ->label($this->lang->translate('Note'))
        ->type('textarea')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->attr('data-plugins', 'autosize')
        ->placeholder('')
        ->value( !empty($this->item['bio'])? $this->fn->q('text')->textarea($this->item['bio']):'' );

# set form
$arr['form'] = '<form class="js-submit-form" data-plugins="empposition" method="post" action="'.URL. 'employees/save"></form>';

# body
$arr['body'] = $form->html();

# title
if( !empty($this->item) ){
    $arr['title']= $title;
    $arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
}
else{
    $arr['title']= $title;
}

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

$arr['width'] = 550;

echo json_encode($arr);