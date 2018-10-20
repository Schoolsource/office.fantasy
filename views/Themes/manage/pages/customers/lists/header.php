<div ref="header" class="listpage2-header clearfix">

	<div ref="actions" class="listpage2-actions">
		<div class="clearfix mbs mtm">

			<ul class="lfloat" ref="actions">
				<li class="mt">
					<h2><i class="icon-users mrs"></i><span> Customers</span></h2>
				</li>

				<li class="mt"><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>

				<li class="divider"></li>

				 <!-- data-plugins="dialog" -->
				<!-- <li class="mt">
					<a href="<?=URL?>customers/import" data-plugins="dialog" class="btn btn-blue"><i class="icon-plus"></i> Import Excel</a>
				</li> -->
				<!-- <li class="mt">
					<a href="<?=URL?>customers/export" class="btn btn-green" target="_blank"><i class="icon-file-excel-o"></i> Export Excel</a>
				</li> -->
				<li class="mt">
					<a href="<?=URL?>customers/set_userpass" data-plugins="dialog" class="btn btn-orange"><i class="icon-retweet"></i> Refresh User&Pass</a>
				</li>

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
					<label class="label">Select sale</label>
					<select ref="selector" name="sale" class="inputtext">
						<option value="">-</option>
						<?php
						foreach ($this->sales as $key => $value) {
							echo '<option value="'.$value['code'].'">'.$value['name'].'</option>';
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
