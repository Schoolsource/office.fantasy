<?php

$url = URL.'employees/';

?><div data-load="<?=URL?>settings/accounts/employees" class="SettingCol offline">

<div class="SettingCol-header"><div class="SettingCol-contentInner">
	<div class="clearfix">
		<ul class="clearfix lfloat SettingCol-headerActions">

			<li><h2><i class="icon-users mrs"></i><span><?=$this->lang->translate('Employees')?></span></h2></li>
			<li><a class="btn js-refresh"><i class="icon-refresh"></i></a></li>
			<li class="divider"></li>

			<li><a class="btn btn-blue" data-plugins="dialog" href="<?=$url?>add"><i class="icon-plus mrs"></i><span><?=$this->lang->translate('Add New')?></span></a></li>
			
		</ul>
		<ul class="rfloat SettingCol-headerActions clearfix">
			<li id="more-link"></li>
		</ul>

	
	</div>

	<div class="mtm clearfix">
		<ul class="lfloat SettingCol-headerActions clearfix">
			<li><label>Department:</label> <select ref="selector" name="department" class="inputtext">
				<option value="">All</option>
			<?php foreach ($this->department as $key => $value) {

				$s = '';
				if( isset($_GET['department']) ){

					if( $_GET['department']==$value['id'] ){
						$s = ' selected="1"';
					}
				}
			 ?>
				<option<?=$s?> value="<?=$value['id']?>"><?=$value['name']?></option>
			<?php } ?>
			</select></li>

			<li><label>Position:</label> <select ref="selector" name="position" class="inputtext">
				<option value="">All</option>
			<?php foreach ($this->position as $key => $value) {


				$s = '';
				if( isset($_GET['position']) ){

					if( $_GET['position']==$value['id'] ){
						$s = ' selected="1"';
					}
				}
				// 
			 ?>
				<option<?=$s?> value="<?=$value['id']?>"><?=$value['name']?></option>
			<?php } ?>
			</select></li>
			<li><label>Display:</label> <select ref="selector" name="display" class="inputtext">
			<?php foreach ($this->display as $key => $value) {
				$s = '';
				if( isset($_GET['display']) ){

					if( $_GET['display']==$value['id'] ){
						$s = ' selected="1"';
					}
				}
			 ?>
				<option<?=$s?> value="<?=$value['id']?>"><?=$value['name']?></option>
			<?php } ?>
			</select></li>

		</ul>
		<ul class="rfloat SettingCol-headerActions clearfix">
			<li>
				<label for="search-query">Search:</label>
				<form class="form-search" action="#">
				<input class="search-input inputtext" type="text" id="search-query" placeholder="ค้นหา" name="q" autocomplete="off">
				<span class="search-icon">
			 		 <button type="submit" class="icon-search nav-search" tabindex="-1"></button>
				</span>

			</form></li>
			
		</ul>
	</div>
	<!-- <div class="setting-description mtm uiBoxYellow pam">Manage your personal employee settings.</div> -->
</div></div>

<div class="SettingCol-main">
	<div class="SettingCol-tableHeader"><div class="SettingCol-contentInner">
		<table class="settings-table admin"><thead><tr>
			<th class="name" data-col="0">Full Name</th>
			<th class="email" data-col="1"></th>
			<th class="actions" data-col="2">Action</th>
		</tr></thead></table>
	</div></div>
	<div class="SettingCol-contentInner">
	<div class="SettingCol-tableBody"></div>
	<div class="SettingCol-tableEmpty empty">
		<div class="empty-loader">
			<div class="empty-loader-icon loader-spin-wrap"><div class="loader-spin"></div></div>
			<div class="empty-loader-text">Loading...</div>
		</div>
		<div class="empty-error">
			<div class="empty-icon"><i class="icon-link"></i></div>
			<div class="empty-title">Connection Error.</div>
		</div>

		<div class="empty-text">
			<div class="empty-icon"><i class="icon-users"></i></div>
			<div class="empty-title">No Results Found.</div>
		</div>
	</div>
	</div>
</div>

</div>