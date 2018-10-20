<?php
require 'init.php';
$title = 'Create';
$id = '';
if (!empty($this->item)) {
    $title = 'Edit';
    $id = $this->item['id'];
}
?>
<div id="mainContainer" class="clearfix" data-plugins="main">

	<div class="profile-left" role="left" data-width="300">

		<div class="profile-left-header" role="leftHeader">

			<div class="profile-left-title">
				<h2><?=$title; ?> an Products</h2>
				<?php if (!empty($this->item['updated'])) {
    ?>
				<div class="fsm">Updated : <?= $this->fn->q('time')->live($this->item['updated']); ?></div>
				<?php
} ?>
			</div>

			<!-- <div id="overviewProfileCompleteness">
                <div class="title">
                    <span id="profileCompletenessLabel" class="label" aria-hidden="true">Profile completeness</span>
                    <span id="profileCompletenessValue" class="value" aria-hidden="true">70%</span>
                </div>
                <div class="progress-bar medium">
                    <span class="progresBarValue" rel="70%" style="width: 70%;"></span>
                </div>
            </div> -->
		</div>
		<!-- end: .profile-left-header -->

		<div class="profile-left-details form-insert-people" role="leftContent">

	    <!--  -->
	    <ul class="nav" style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"><?php

            $section_name = '';

            foreach ($list as $key => $value) {
                $cls = '';
                if ($this->section == $value['section']) {
                    $cls .= !empty($cls) ? ' ' : '';
                    $cls .= 'active';

                    $section_name = $value['label'];
                }

                if (!empty($cls)) {
                    $cls = ' class="'.$cls.'"';
                }

                echo '<li'.$cls.'><a href="'.URL.'products/settings/'.$value['section'].'/'.$id.'">'.$value['label'].'</a></li>';
            } ?>

	    </ul>

	    <?php if (!empty($this->item)) {
                ?>
		<div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
		<div class="mvm">
			<label class="checkbox fwb"><input<?=($this->item['pds_status'] == 'A') ? ' checked="1"' : ''; ?> type="checkbox" name="pds_status" data-plugins="_update" data-options="<?=$this->fn->stringify(array(
                'url' => URL.'products/_update/'.$this->item['id'].'/pds_status',
            )); ?>" /> Active</label>
		</div>
	    <div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
		<div class="mvm">
			<label class="checkbox fwb"><input<?=!empty($this->item['pds_has_vat']) ? ' checked="1"' : ''; ?> type="checkbox" name="pds_has_vat" data-plugins="_update" data-options="<?=$this->fn->stringify(array(
                'url' => URL.'products/_update/'.$this->item['id'].'/pds_has_vat',
            )); ?>" /> VAT Product</label>
		</div>
		<div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
		<div class="mvm">
			<label class="checkbox fwb"><input<?=!empty($this->item['pds_is_show']) ? ' checked="1"' : ''; ?> type="checkbox" name="pds_is_show" data-plugins="_update" data-options="<?=$this->fn->stringify(array(
                'url' => URL.'products/_update/'.$this->item['id'].'/pds_is_show',
            )); ?>" /> On Website</label>
		</div>
		<div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
		<div class="mvm">
			<label class="checkbox fwb"><input<?=!empty($this->item['pds_is_mobile']) ? ' checked="1"' : ''; ?> type="checkbox" name="pds_is_mobile" data-plugins="_update" data-options="<?=$this->fn->stringify(array(
                'url' => URL.'products/_update/'.$this->item['id'].'/pds_is_mobile',
            )); ?>" /> On Mobile</label>
		</div>
		<div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
		<div class="mvm">
			<label class="checkbox fwb"><input<?=!empty($this->item['pds_is_hot']) ? ' checked="1"' : ''; ?> type="checkbox" name="pds_is_hot" data-plugins="_update" data-options="<?=$this->fn->stringify(array(
                'url' => URL.'products/_update/'.$this->item['id'].'/pds_is_hot',
            )); ?>" /> Hot Product</label>
		</div>
		<div style="box-shadow: rgba(255, 255, 255, .5) 0px 1px 0px 0px;border-bottom: 1px solid rgb(211, 211, 211);"></div>
	    <!-- <div class="mvm">
	    	<a style="width: 100%;text-align: left;" class="btn btn-red" href="<?=URL; ?>products/del/<?=$id; ?>" data-plugins="dialog"><i class="icon-trash-o mrs"></i><span>Remove</span></a>
	    </div> -->
	    <?php
            } ?>

	    </div>
	    <!-- end: .profile-left-details -->
	</div>
	<div role="content">
		<div role="toolbar" class="cashier-toolbar">
			<div class="mhl phl ptl clearfix">

				<h1 style="display: inline-block;"><?=(!empty($this->item['pds_name']) ? $this->item['pds_name'] : 'New Product'); ?></h1>
				<div>Settings -> <?=$section_name; ?></div>
			</div>
		</div>
		<!-- End: toolbar -->

		<div class="" role="main">
			<?php require 'main.php'; ?>
		</div>
		<!-- end: main -->

	</div>
	<!-- end: content -->
</div>
