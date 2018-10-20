<div class="pll mhl mtl">
<ul class="uiList settingsList">
    <?php foreach ($list as $key => $value) {

        $class = '';

        if( $this->section == $value['section'] ){
            $class .= !empty($class) ? ' ':'';
            $class .= 'openPanel';
        }

     ?>
    <li class="<?=$class?>">
        <div class="clearfix settingsListLink hidden_elem">
            <div class="label"><?=$value['label']?></div>
        </div>
        <div class="content">
            <form class="js-submit-form" action="<?=URL?>products/update/<?=$this->section?>/<?=$id?>" enctype="multipart/form-data">
                <?php
                if( $this->section == $value['section'] ){
                    require "sections/{$value['section']}.php";
                }
                if( !empty($this->item) ){
                    echo '<input type="hidden" autocomplete="off" class="hiddenInput" value="'.$this->item['id'].'" name="id">';
                }
                ?>
                <div class="mtl clearfix">
                    <button type="submit" class="btn-submit btn btn-green lfloat"><i class="icon-floppy-o"></i> Save</button>
                    <a href="<?=URL?>products" class="btn btn-red rfloat"><i class="icon-remove"></i> Back</a>
                </div>
            </form>
        </div>
    </li>
    <?php } ?>
</ul>
</div>
