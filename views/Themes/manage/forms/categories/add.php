<?php

$title = 'Categories';
if( !empty($this->item) ){
	$arr['title'] = "Edit {$title}";
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=>$this->item['id']);
}
else{
	$arr['title'] = "Add {$title}";
}

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("img")
		->label("Upload a photo (350px * 350px)")
		->type("file")
		->addClass('inputtext')
		->autocomplete('off')
		->value( '' );

if( !empty($this->item["image_arr"]) ){
	$form 	->hr('<div class="uiBoxWhite mbl pam" style="margin-top:-3mm;">
					<span class="gbtn rfloat">
						<a data-plugins="dialog" href="'.URL.'categories/del_image/'.$this->item['cate_img_id'].'" class="btn btn-red btn-no-padding"><i class="icon-remove"></i></a>
					</span>
					<img class="pam pic" src="'.$this->item['image_url'].'">
				</div>');
}

$form 	->field("firstCode")
		->label("Prologue *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['fristCode']) ? $this->item['fristCode'] : '' );

$form 	->field("name_th")
		->label("Thai name *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['name_th']) ? $this->item['name_th'] : '' );

$form 	->field("name_en")
		->label("English name *")
		->autocomplete('off')
		->addClass('inputtext')
		->value( !empty($this->item['name_en']) ? $this->item['name_en'] : '' );

$ck = '';
if( !empty($this->item) ){
	if( !empty($this->item['is_sub']) ) $ck='checked="1"';
}
$is_sub = '<label class="checkbox control-label"><input type="checkbox" '.$ck.' name="is_sub" value="1"> Make a sub menu of another category.</label>';
$form 	->field("is_sub")
		->text( $is_sub );

$form 	->field("cate_id")
		->label("Main category")
		->addClass('inputtext')
		->select( $this->category['lists'], 'id', 'name_th' )
		->value( !empty($this->item['cate_id']) ? $this->item['cate_id'] : '' );

$form 	->field('status')
		->label('Status')
		->autocomplete('off')
		->addClass('inputtext')
		->select($this->status)
		->value( !empty($this->item['status']) ? $this->item['status'] : '' );

# set form
$arr['form'] = '<form class="js-submit-form" data-plugins="formCategory" method="post" action="'.URL. 'categories/save"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['is_close_bg'] = true;

echo json_encode($arr);
