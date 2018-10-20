<?php

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("pds_howtouse")
		->label("How to use")
		->addClass('inputtext')
		->type('textarea')
		->autocomplete('off')
		->attr('data-plugins', 'editor2')
		// ->attr('data-options', $this->fn->stringify(array(
  //           'image_upload_url' => URL .'media/set',
  //           'album_obj_type'=>'pds_detail',
  //           'album_obj_id'=>'3'
  //       )))
        ->value( !empty($this->item['pds_howtouse']) ?$this->fn->q('text')->strip_tags_editor(  $this->item['pds_howtouse']): '' );

$form 	->field("pds_capacity")
		->label("Product dimensions")
		->addClass('inputtext')
		->autocomplete('off')
		->value( !empty($this->item['pds_capacity']) ? $this->item['pds_capacity'] : '' );

// $form   ->submit()
//         ->addClass('btn btn-green btn-submit')
//         ->value('บันทึก');

echo $form->html();
