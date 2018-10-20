<?php

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$count = 1;
for ($i=1; $i <= 3 ; $i++) {
	$form 	->field("image_{$i}")
			->name("image[$i]")
			->label("Upload a photo {$i} (350px * 350px)")
			->type("file")
			->addClass('inputtext')
			->autocomplete('off')
			->value( '' );

	if( !empty($this->item['photos'][$i]) ){
		$form 	->hr('<div class="uiBoxWhite mbl pam" style="margin-top:-3mm;">
						<span class="gbtn rfloat">
							<a data-plugins="dialog" href="'.URL.'products/del_image/'.$this->item['photos'][$i]['id'].'" class="btn btn-red btn-no-padding"><i class="icon-remove"></i></a>
						</span>
						<h4 class="fwb">รูปสินค้า '.$i.'</h4>
						<img class="pam pic" src="'.$this->item['photos'][$i]['url'].'">
					 </div>');
	}

	$form 	->hr('<input type="hidden" name="seq['.$i.']" value="'.$i.'">');
	$count++;
}

$form 	->hr('<input type="hidden" name="count" value="'.$count.'"');
echo $form->html();
