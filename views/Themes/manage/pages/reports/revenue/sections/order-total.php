<div class="clearfix">
	<h3 class="fwb"><i class="icon-info-circle"></i> Total ( of day <?=$this->periodStr?>)</h3>
	<div class="ReportSummary_numberList ">
        <div class="ReportSummary_numberItem subtotal-text">
            <div><span class="value"><?=number_format($this->results['total'])?></span></div>
            <div>Order</div>
        </div>

        <div class="ReportSummary_numberItem total-text">
            <div><span class="value"><?=number_format($this->results['price'], 2)?>฿</span></div>
            <div>Price</div>
        </div>
    </div>
</div>
