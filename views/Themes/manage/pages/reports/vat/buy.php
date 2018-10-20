<div id="mainContainer" class="report-main clearfix" data-plugins="main">
	<div role="content">
		<div role="main" class="pal">
			<div class="uiBoxWhite pas pam">
				<div class="clearfix">
					<div class="lfloat">
						<h3 class="fwb"><i class="icon-diamond"></i> VAT Buy Report</h3>
					</div>
				</div>
				<div class="clearfix">
					<div class="lfloat" style="margin-left: 5mm;">
                        <ul>
                            <li class="js-control mtm">
                                <label class="label" for="closedate">Choose date</label>
								<select selector="closedate" name="closedate" class="inputtext" style="display:inline;"></select>
                          
								<label class="label">Credit</label>
								<select id="credit" name="credit" class="inputtext" style="display:inline;">
									 <option value="">- All -</option>
									<?php
									foreach ($this->credit as $key => $value) {
										echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
									}
									?>
								</select>

								<label class="label">Category</label>
								<select id="category" name="category" class="inputtext" style="display:inline;">
									 <option value="">- All -</option>
									<?php
									foreach ($this->category as $key => $value) {
										echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
									}
									?>
								</select>

								<label class="label">Report</label>
								<select id="report" name="report" class="inputtext" style="display:inline;">
									<option value="">- All -</option>
									<option value="1">Yes</option>
									<!--option value="0">No</option-->
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
				var credit   = $('#credit').val();
				var category = $('#category').val();
				var report  = $('#report').val();
				$.get(Event.URL + 'reports/vatbuy', {period_start:date.startDateStr, period_end:date.endDateStr, credit:credit, category:category, report:report}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
			},
		});

		$('#credit').change(function(){
			var credit   = $('#credit').val();
			var category = $('#category').val();
			var report  = $('#report').val();
			var start_date = $('input[name=start_date]').val();
			var end_date = $('input[name=end_date]').val();
			$.get(Event.URL + 'reports/vatbuy', {period_start:start_date, period_end:end_date, credit:credit, category:category, report:report}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
		});

		$('#category').change(function(){
			var credit   = $('#credit').val();
			var category = $('#category').val();
			var report  = $('#report').val();
			var start_date = $('input[name=start_date]').val();
			var end_date = $('input[name=end_date]').val();
			$.get(Event.URL + 'reports/vatbuy', {period_start:start_date, period_end:end_date, credit:credit, category:category, report:report}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
		});

		$('#report').change(function(){
			var credit   = $('#credit').val();
			var category = $('#category').val();
			var report  = $('#report').val();
			var start_date = $('input[name=start_date]').val();
			var end_date = $('input[name=end_date]').val();
			$.get(Event.URL + 'reports/vatbuy', {period_start:start_date, period_end:end_date, credit:credit, category:category, report:report}, function(res){
					$('#table-lists').html( res );
					Event.plugins( $('#table-lists') );
				});
		});

	});
</script>
<!--script type="text/javascript">
$(function() {
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
			getVatbuy();
		},
	});

	$('[select[name=credit]').change(function() {
		getVatbuy();
	});

	$('[select[name=category]').change(function() {
		getVatbuy();
	});

	function getVatbuy() {
	    var credit = $('select[name=credit]').val();
        var category = $('select[name=category]').val();
        var start_date = $('input[name=start_date]').val();
        var end_date = $('input[name=end_date]').val();
        $.get(Event.URL + 'reports/vatbuy', {period_start:start_date, period_end:end_date, credit:credit, category:category, main:1}, function(res){
            $('#table-lists').html( res );
            Event.plugins( $('#table-lists') );
        });
	}
});
</script-->
