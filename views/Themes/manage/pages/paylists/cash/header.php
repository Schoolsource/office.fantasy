<?php
$icon = "icon-";
if( $this->type['id'] == 1 ){
	$icon .= "money";
}
elseif( $this->type['id'] == 2 ){
	$icon .= "cc-visa";
}
elseif( $this->type['id'] == 3 ){
	$icon .= "credit-card-alt";
}
?>
<div ref="header" class="listpage2-header clearfix">

	<div ref="actions" class="listpage2-actions">
		<div class="clearfix mbs mtm">

			<ul class="lfloat" ref="actions">
				<li class="mt">
					<?php $name = str_replace("จ่าย", "", $this->type['name']); ?>
					<h2><i class="<?=$icon?> mrs"></i><span> Receipts <?=$name?></span></h2>
				</li>

				<li class="mt"><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>

				<li class="divider"></li>

			</ul>

			<ul class="lfloat selection hidden_elem" ref="selection">
				<li><span class="count-value"></span></li>
				<li><a class="btn-icon"><i class="icon-download"></i></a></li>
				<li><a class="btn-icon"><i class="icon-trash"></i></a></li>
			</ul>


			<ul class="rfloat" ref="control">
				<li><label class="fwb fcg fsm" for="limit">Show</label>
				<select ref="selector" id="limit" name="limit" class="inputtext"><?php
					echo '<option value="20">20</option>';
					echo '<option selected value="50">50</option>';
					echo '<option value="100">100</option>';
					echo '<option value="200">200</option>';
				?></select><span id="more-link">Loading...</span></li>
			</ul>

		</div>
		<div class="clearfix mbl mtm">
			<ul class="lfloat" ref="control">
				<li>
					<label for="closedate" class="label">Choose date</label><select ref="closedate" name="closedate" class="inputtext">
						<option value="daily">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="weekly" selected>This week</option>
						<option value="monthly">This month</option>
						<option value="custom">Custom</option>
					</select>
				</li>

				<li class="divider"></li>

				<li>
					<label class="label" for="bank">Select a bank</label>
					<select name="bank" class="inputtext" ref="selector">
						<option value="">-</option>
						<?php
						foreach ($this->bank as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
						}
						?>
					</select>
				</li>

			</ul>
			<ul class="rfloat" ref="control">
				<li class="mt"><form class="form-search" action="#">
					<input class="inputtext search-input" type="text" id="search-query" placeholder="<?=$this->lang->translate('Search')?>" name="q" autocomplete="off">
					<span class="search-icon">
						<button type="submit" class="icon-search nav-search" tabindex="-1"></button>
					</span>

				</form></li>
			</ul>
		</div>

	</div>

</div>
