<?php

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$menu = '';
$i = 1;

foreach ($this->pageMenu as $key => $value) {
	$checked = ''; $ck_add = ''; $ck_edit = ''; $ck_del = '';

	if( !empty($this->item['permission'][$value['id']]) ){

		foreach ($this->item['permission'][$value['id']] as $val) {

			if( $val == 'view' ){
				$checked = ' checked="1"';
			}

			if( $val == 'add' ){
				$ck_add = ' checked="1"';
			}

			if( $val == 'edit' ){
				$ck_edit = ' checked="1"';
			}

			if( $val == 'del' ){
				$ck_del = ' checked="1"';
			}
		}
	}

	if( empty($value['sub']) ){
		// $menu .= '<div class="fullname fwb">'.$value['name'].'</div>';

		if( $value['key'] =='dashboard' || $value['key'] == 'reports' || $value['key'] == 'calendar' ){
			$menu .= ' <label class="checkbox"><input'.$checked.' type="checkbox" name="dep_permission['.$value['id'].'][]" value="view">ดู</label>';
		}
		else{
			$menu .= ' <label class="checkbox"><input'.$ck_add.' type="checkbox" name="dep_permission['.$value['id'].'][]" value="add">เพิ่ม</label>';
			$menu .= ' <label class="checkbox"><input'.$ck_edit.' type="checkbox" name="dep_permission['.$value['id'].'][]" value="edit">แก้ไข</label>';
			$menu .= ' <label class="checkbox"><input'.$ck_del.' type="checkbox" name="dep_permission['.$value['id'].'][]" value="del">ลบ</label>';
		}

$form   ->field($value['name'])
		->label($value['name'])
		->text( $menu );

	$menu = '';
	}
	else{
		/*if( $i==1 ) $menu .= '<div class="fullname fwb">ตั้งค่า</div>';
		$i++;*/

		if( !empty($this->item['permission']['settings']) ){

			$arr_settings = $this->item['permission']['settings'];
			if( in_array($value['id'], $arr_settings) ) $checked = ' checked="1"';
		}

		$menu .= ' <label class="checkbox"><input'.$checked.' type="checkbox" name="dep_permission['.$value['key'].'][]" value="'.$value['id'].'">'.$value['name'].'</label>';

		$form   ->field('settings')
			->label('ตั้งค่า')
			->text( $menu );
		$menu = '';
	}	

}

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'employees/save_permit_department"></form>';

# body
$arr['body'] = $form->html();

# title
$arr['title']= "{$this->lang->translate('Permission')}";
$arr['hiddenInput'][] = array('name'=>'id','value'=>$this->item['id']);
$arr['hiddenInput'][] = array('name'=>'section','value'=>'permit');


# fotter: button
$arr['width'] = 620;
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);