<?php

$form = new Form();
$form = $form->create()
             // set From
             ->elem('div')
             // ->url( URL.'products/update/' . $this->section )
             // ->method('post')
             ->addClass('form-insert');

$form->field('pds_categories_id')
     ->label('Categories')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->select($this->category, 'id', 'name_th')
     ->value(!empty($this->item['pds_categories_id']) ? $this->item['pds_categories_id'] : '');

$form->field('pds_code')
     ->label('Product code')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->value(!empty($this->item['pds_code']) ? $this->item['pds_code'] : '');

$form->field('pds_name')
     ->label('Product name')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->value(!empty($this->item['pds_name']) ? $this->item['pds_name'] : '');

$form->field('pds_unit')
     ->label('Product Unit')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->value(!empty($this->item['pds_unit']) ? $this->item['pds_unit'] : '');

$form->field('pds_detail')
     ->label('Description')
     ->addClass('inputtext')
     ->type('textarea')
     ->attr('data-plugins', 'editor2')
     // ->attr('data-options', $this->fn->stringify(array(
     //     'image_upload_url' => URL .'media/set',
     //     'album_obj_type'=>'products',
     //     'album_obj_id'=>'2'
     // )))
     ->autocomplete('off')
     ->value(!empty($this->item['pds_detail']) ? $this->fn->q('text')->strip_tags_editor($this->item['pds_detail']) : '');

$form->field('pds_barcode')
     ->label('Barcode')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->value(!empty($this->item['pds_barcode']) ? $this->item['pds_barcode'] : '');

// $form   ->submit()
//         ->addClass('btn btn-blue btn-submit')
//         ->value('บันทึก');

echo $form->html();
