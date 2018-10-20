<?php
$form = new Form();
$form = $form->create()
             ->elem('div')
             ->addClass('form-insert');

$form->field('imp_date')
        ->label('DATE')
        ->attr('data-plugins', 'datepicker')
        ->type('date')
        ->addClass('inputtext')
        ->autocomplete('off')
        ->value(!empty($this->item['date']) ? $this->item['date'] : '');

$form->field('imp_supplier')
        ->label('Supplier')
        ->addClass('inputtext')
        ->autocomplete('off')
        ->value('');

$form->field('imp_code')
        ->label('Invoice No.')
        ->addClass('inputtext')
        ->autocomplete('off')
        ->value(!empty($this->item['code']) ? $this->item['code'] : '');

// $form 	->field("imp_peho")
// 		->label("PECO")
// 		->addClass("inputtext")
// 		->autocomplete("off")
// 		->value( !empty($this->item["peho"]) ? $this->item["peho"] : '' );

$formTable = new Form();
$formTable = $formTable->create()
                       ->elem('div');

$table = '<table class="table-bordered" width="100%">
				<thead>
					<tr style="background-color: #0054A3; color:#fff;">
						<th width="5%">#</th>
						<th width="40%">Product</th>
						<th width="10%">QTY</th>
						<th width="10%">Unit</th>
						<th width="10%">Price/Unit</th>
						<th width="10%">Amount</th>
						<th width="10%">Action</th>
					</tr>
				</thead>
				<tbody role="listsitem">
				</tbody>
			</table>';

$formTable->field('lists')
            ->text($table);

$options = $this->fn->stringify(array(
        'sup_id' => !empty($this->item['sup_id']) ? $this->item['sup_id'] : '',
        'items' => !empty($this->item['items']) ? $this->item['items'] : array(),
        'products' => $this->products,
));
?>
<div id="mainContainer" class="clearfix" data-plugins="main">
	<div class="clearfix">
		<div class="span12">
			<div role="toolbar" class="mtm">
				<h3 class="fwb"><i class="icon-product-hunt mrs"></i> Import Product</h3>
			</div>
			<div role="main">
				<form class="js-submit-form" action="<?=URL; ?>import/save" data-plugins="importForm" data-options="<?=$options; ?>">
					<div class="uiBoxWhite pal mtm">
						<h3 class="fwb mbs"><i class="icon-home"></i> Infomation</h3>
						<?=$form->html(); ?>
					</div>
					<div class="uiBoxWhite pal mtm form-insert">
						<h3 class="fwb mbs"><i class="icon-list"></i> Products List</h3>
						<?=$formTable->html(); ?>
					</div>
					<div class="uiBoxWhite pal mts">
						<div class="clearfix">
							<div class="rfloat">
								<table>
									<tr>
										<td width="50%" class="fwb">Total product</td>
										<td width="50%" class="tar"><span summary="item"></span></td>
									</tr>
									<tr>
										<td width="50%" class="fwb">Total amount</td>
										<td width="50%" class="tar"><span summary="total"></span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="uiBoxWhite pal mts">
						<div class="clearfix">
							<a href="<?=URL; ?>import" class="btn btn-red">Cancel</a>
							<button type="submit" class="js-submit btn btn-blue rfloat">Save</button>
						</div>
					</div>
					<input type="hidden" name="imp_total_price" value="0">
					<?php if (!empty($this->item)) {
    echo '<input type="hidden" name="id" value="'.$this->item['id'].'">';
}?>
				</form>
			</div>
		</div>
	</div>
</div>