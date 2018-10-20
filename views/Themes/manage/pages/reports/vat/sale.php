<div id="mainContainer" class="report-main clearfix" data-plugins="main">
	<div role="content">
		<div role="main" class="pal">
			<div class="uiBoxWhite pas pam">
				<div class="clearfix">
					<div class="lfloat">
						<h3 class="fwb"><i class="icon-university"></i> VAT Sale Report</h3>
					</div>
				</div>
				<div class="clearfix">
					<div class="lfloat" style="margin-left: 5mm;">
						<ul>
							<li class="js-control mtm">
								<label class="label" for="closedate">Choose date</label>
								<select selector="closedate" name="closedate" class="inputtext" style="display:inline;"></select>
						
								<label class="label" for="payment">Payment type</label>
								<select id="term_of_payment" name="term_of_payment" class="inputtext" style="display:inline;">
									<option value="">- All -</option>
									<?php
									foreach ($this->term_of_payment as $key => $value) {
										echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
									}
									?>
								</select>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="uiBoxWhite pas pam" style="margin-top: 2mm;">
				<div id="table-lists"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$('[selector=closedate]').closedate({
			leng:"th",
			options: [
				{
					text: 'Today',
					value: 'daily',
				},
				{
					text: 'Yesterday',
					value: 'yesterday',
				},
				{
					text: 'This week',
					value: 'weekly',
				},
				{
					text: 'This month',
					value: 'monthly',
				},
				{
					text: 'Custom',
					value: 'custom',
				}
			],
			onChange:function(date){
				var term_of_payment = $('#term_of_payment').val();
				$.get(Event.URL + 'reports/vatsale', {period_start:date.startDateStr, period_end:date.endDateStr, term_of_payment:term_of_payment}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
			},
		});

		$('#term_of_payment').change(function(){
			var term_of_payment = $('#term_of_payment').val();
			var start_date = $('input[name=start_date]').val();
			var end_date = $('input[name=end_date]').val();
			$.get(Event.URL + 'reports/vatsale', {period_start:start_date, period_end:end_date, term_of_payment:term_of_payment}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
		});
	});
</script>
