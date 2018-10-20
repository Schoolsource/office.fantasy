<div ref="header" class="listpage2-header clearfix">

	<div ref="actions" class="listpage2-actions">
		<div class="clearfix mbs mtm">

			<ul class="lfloat" ref="actions">
				<li>
					<h2><i class="icon-money mrs"></i><span>Receipts Report</span></h2>
				</li>

				<li><a class="btn js-refresh" data-plugins="tooltip" data-options="<?=$this->fn->stringify(array('text'=>'refresh'))?>"><i class="icon-refresh"></i></a></li>

				<!-- <li class="divider"></li> -->

			</ul>


			<!-- <ul class="rfloat" ref="control">
				<li><label class="fwb fcg fsm" for="limit">Show</label>
				<select ref="selector" id="limit" name="limit" class="inputtext"><?php
					echo '<option value="20">20</option>';
					echo '<option selected value="50">50</option>';
					echo '<option value="100">100</option>';
					echo '<option value="200">200</option>';
				?></select><span id="more-link">Loading...</span></li>
			</ul> -->

			<ul class="rfloat" ref="control">
				<li><button type="button" class="btn btn-blue" ref="form" data-url="<?=URL?>pdf/receiptReport"><i class="icon-file-pdf-o"></i><span class="mls">Export PDF</span></button></li>
			</ul>

		</div>
		<div class="clearfix mbl mtm">
			<ul class="lfloat" ref="control">
				<li>
					<label for="closedate" class="label">Choose date</label><select ref="closedate" name="closedate" class="inputtext">
						<option value="daily">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="weekly">This week</option>
						<option value="monthly" selected>This month</option>
						<option value="custom">Custom</option>
					</select>
				</li>

				<li class="divider"></li>

				<li>
					<label class="label" for="account">Select a bank</label>
					<select name="account" class="inputtext" ref="selector">
						<option value="">- ทั้งหมด -</option>
						<?php
						foreach ($this->account as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['name_str'].'</option>';
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
