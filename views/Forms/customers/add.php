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

if( !empty($this->item['id']) ){
    $options['setdata_url'] = URL.'customers/setdata/'.$this->item['id'].'/cus_image_id/?has_image_remove';
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
    ->addClass('form-insert pal');

// $form   ->field("image")
//         ->text( $picture_box );

$form   ->field("name")
        ->label('ชื่อ')
        ->text( $this->fn->q('form')->fullname( !empty($this->item)?$this->item:array(), array('field_first_name'=>'cus_', 'prefix_name'=>$this->prefixName) ) );

$birthday = array();
if( !empty($this->item['birthday']) ){
    if( $this->item['birthday'] != '0000-00-00' ){
        $birthday = $this->item;
    }
}

$form   ->field("birthday")
        ->label('วันเกิด')
        ->text( $this->fn->q('form')->birthday( $birthday, array('field_first_name'=>'cus_') ) );

$form   ->field("cus_card_id")
        ->label('หมายเลขบัตรประจำตัวประชาชน')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->placeholder('')
        ->value( !empty($this->item['card_id'])? $this->item['card_id']:'' );

$form   ->field("cus_address")
        ->name('cus[address]')
        ->label('ที่อยู่')
        ->text( $this->fn->q('form')->address( !empty($this->item['address'])? $this->item['address']:array(), array('city'=>$this->city ) ) );

// email
$form->hr( $this->fn->q('form')->contacts( 
    'email',
    !empty($this->item['options']['email'])? $this->item['options']['email']:array(), 
    array( 'field_first_name'=>'options[email]' )
) );

// phone
$form->hr( $this->fn->q('form')->contacts( 
    'phone',
    !empty($this->item['options']['phone'])? $this->item['options']['phone']:array(), 
    array( 'field_first_name'=>'options[phone]' )
) );

// social
$form->hr( $this->fn->q('form')->contacts( 
    'social',
    !empty($this->item['options']['social'])? $this->item['options']['social']:array(), 
    array( 'field_first_name'=>'options[social]' )
) );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'customers/save"></form>';

# body
$arr['body'] = $form->html();

if( !empty($this->item) ){
    $arr['title']= "แก้ไขลูกค้า";
    $arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
}
else{
    $arr['title']= "เพิ่มลูกค้า";
}

$arr['width'] = 550;
$arr['height'] = 'full';
$arr['overflowY'] = 'auto';

$rolesubmit = '';
if( isset( $_REQUEST['callback'] ) ){
    $arr['hiddenInput'][] = array('name'=>'callback','value'=>$_REQUEST['callback']);
    $rolesubmit = ' role="submit"';
}

$arr['button'] = '<button type="submit"'.$rolesubmit.' class="btn btn-primary btn-submit"><span class="btn-text">บันทึก</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">ยกเลิก</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);