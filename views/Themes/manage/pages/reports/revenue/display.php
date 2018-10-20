<div id="mainContainer" class="report-main clearfix" data-plugins="main">
    <div role="content">
        <div role="main" class="pal">
            <div class="uiBoxWhite pas pam">
                <div class="clearfix">
                    <div class="lfloat">
                        <h3 class="fwb"><i class="icon-line-chart mrs"></i>Sale Reports</h3>
                    </div>
                    <div class="lfloat" style="margin-left: 5mm;">
                        <ul>
                            <li class="clearfix">
                                <label class="label" for="closedate">Choose date</label>
								<select selector="closedate" name="closedate" class="inputtext"></select>
								<!-- <label for="closedate" class="label">Choose date</label>
								<select ref="closedate" name="closedate" class="inputtext">
									<option value="daily">Today</option>
									<option value="yesterday">Yesterday</option>
									<option value="weekly">This week</option>
									<option value="monthly" selected>This month</option>
									<option value="custom">Custom</option>
								</select> -->
                            </li>
                        </ul>
                    </div>
                    <div class="lfloat" style="margin-left: 5mm;">
						<ul>
                            <li class="clearfix">
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
					</div>
					<div class="lfloat" style="margin-left: 5mm;">
						<ul>
                            <li class="clearfix">
							<label class="label">Term of payment</label>
							<select ref="selector" name="term_of_payment" class="inputtext">
								<option value="">-</option>
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
                <div id="total"></div>
            </div>
            <div class="uiBoxWhite pas pam" style="margin-top: 2mm;">
                <div id="table-lists"></div>
            </div>
        </div>
    </div>
</div>
<style>
    .uiPopover {
        display: block;
    }
</style>
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
            getRevenue();
            // var sale = $('select[name=sale]').val();
            // var term_of_payment = $('select[name=term_of_payment]').val();
            // $.get(Event.URL + 'reports/revenue', {period_start:date.startDateStr, period_end:date.endDateStr, sale:sale, term_of_payment:term_of_payment, main:1}, function(res){
            //     $('#table-lists').html( res );
            //     Event.plugins( $('#table-lists') );
            // });
            // $.get(Event.URL + 'reports/revenue_total', {period_start:date.startDateStr, period_end:date.endDateStr, sale:sale, term_of_payment:term_of_payment, main:1}, function(res){
            //     $('#total').html( res );
            //     Event.plugins( $('#total') );
            // });
        },
    });

    $('select[name=sale]').change(function(){
        getRevenue();
    });

    $('select[name=term_of_payment]').change(function(){
        getRevenue();
    });

    function getRevenue()
    {
        var sale = $('select[name=sale]').val();
        var term_of_payment = $('select[name=term_of_payment]').val();
        var startDateStr = $('input[name=start_date]').val();
        var endDateStr = $('input[name=end_date]').val();
        $.get(Event.URL + 'reports/revenue', {period_start:startDateStr, period_end:endDateStr, sale:sale, term_of_payment:term_of_payment, main:1}, function(res){
            $('#table-lists').html( res );
            Event.plugins( $('#table-lists') );
        });
        $.get(Event.URL + 'reports/revenue_total', {period_start:startDateStr, period_end:endDateStr, sale:sale, term_of_payment:term_of_payment, main:1}, function(res){
            $('#total').html( res );
            Event.plugins( $('#total') );
        });
    }
});

</script>
