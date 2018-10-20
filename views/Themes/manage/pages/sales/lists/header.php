<div ref="header" class="listpage2-header clearfix">

	<div ref="actions" class="listpage2-actions">
		<div class="clearfix mbs mtm">

			<ul class="lfloat" ref="actions">
				<li class="mt">
					<h2><i class="icon-user mrs"></i><span> Sales</span></h2>
				</li>

				<li class="mt"><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>

				<li class="divider"></li>

				 <!-- data-plugins="dialog" -->
				<li class="mt">
					<a class="btn btn-blue" data-plugins="dialog" href="<?=URL?>sales/add"><i class="icon-plus mrs"></i>
						<span><?=$this->lang->translate('Add New')?></span>
					</a>

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
				<!-- <li>
					<label for="closedate" class="label">เลือกวันที่</label><select ref="closedate" name="closedate" class="inputtext">
						<option value="daily">วันนี้</option>
						<option value="yesterday">เมื่อวานนี้</option>
						<option value="weekly" selected>สัปดาห์นี้</option>
						<option value="monthly">เดือนนี้</option>
						<option value="custom">กำหนดเอง</option>
					</select>
				</li> -->

				<li>
					<label for="status" class="label">Select status</label>
					<select ref="selector" name="status" class="inputtext">
						<?php
						echo '<option value="">--- Select status ---</option>';
						foreach ($this->status as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
						}
						?>
					</select>
				</li>

				<li class="divider"></li>

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
