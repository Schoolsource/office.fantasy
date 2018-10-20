<?php

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

for($i=1;$i<=3;$i++){
$form   ->field("image".$i)
        ->label('รูปสินค้า'.$i)
        ->text('<div class="profile-cover image-cover pas" data-plugins="imageCover" data-options="'.(
        !empty($this->item['image_'.$i.'_arr']) 
            ? $this->fn->stringify( array_merge( 
                array( 
                    'scaledX'=> 720,
                    'scaledY'=> 720,
                    'action_url' => URL.'products/del_image_cover/'.$this->item['id'],
                    // 'top_url' => IMAGES_PRODUCTS
                ), $this->item['image_'.$i.'_arr'] ) )
            : $this->fn->stringify( array( 
                    'scaledX'=> 720,
                    'scaledY'=> 720
                ) )
            ).'">
        <div class="loader">
        <div class="progress-bar medium"><span class="bar blue" style="width:0"></span></div>
        </div>
        <div class="preview"></div>
        <div class="dropzone">
            <div class="dropzone-text">
                <div class="dropzone-icon"><i class="icon-picture-o img"></i></div>
                <div class="dropzone-title">ขนาด 350x350 px</div>
            </div>
            <div class="media-upload"><input type="file" accept="image/*" name="image_cover['.$i.']"></div>
        </div>
        
</div>');
}

echo $form->html();