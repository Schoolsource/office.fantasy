<?php
$form = new Form();
$form = $form->create()
             ->elem('div')
             ->addClass('form-insert');

$form->field('bill_customer')
     ->label('ลูกค้า*')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->value('');

$form->field('bill_address')
     ->label('ที่อยู่')
     ->text('<div id="address">-</div>');

$form->field('bill_send_date')
     ->label('วันที่ส่งของ*')
     ->addClass('inputtext')
     ->autocomplete('off')
     ->type('date')
     ->attr('data-plugins', 'datepicker')
     ->value(!empty($this->item['send_date']) ? $this->item['send_date'] : '');

$ck_30 = '';
$ck_00 = '';
$ck_credit = '';
$ck_transfer = '';

if (empty($this->item)) {
    $ck_30 = ' checked="1"';
} else {
    if ($this->item['term_of_payment'] == 1) {
        $ck_00 = ' checked="1"';
    } elseif ($this->item['term_of_payment'] == 2) {
        $ck_30 = ' checked="1"';
    } elseif ($this->item['term_of_payment'] == 3) {
    	$ck_credit = ' checked="1"';
    } else {
    	$ck_transfer = ' checked="1"';
    }
}

$term_of_payment = '<div>
						<label class="radio">
							<input'.$ck_30.' type="radio" name="bill_term_of_payment" value="2" data-date="30"> เครดิต 30 วัน
						</label>
					</div>
					<div class="mts">
						<label class="radio">
							<input'.$ck_00.' type="radio" name="bill_term_of_payment" value="1" data-date="0"> เงินสด
						</label>
					</div>
					<div class="mts">
						<label class="radio">
							<input'.$ck_credit.' type="radio" name="bill_term_of_payment" value="3" data-date="0"> บัตรเครดิต
						</label>
					</div>
					<div class="mts">
						<label class="radio">
							<input'.$ck_transfer.' type="radio" name="bill_term_of_payment" value="5" data-date="0"> โอนเงิน
						</label>
					</div>';

$form->field('bill_term_of_payment')
        ->label('เงื่อนไขการชำระเงิน')
        ->text($term_of_payment);

$form->field('bill_submit_date')
        ->label('วันที่ถึงกำหนด')
        ->text('<div id="submit_date" class="fwb"></div>');

$form->hr('<input type="hidden" name="bill_submit_date" value="">');

// $form 	->field("bill_submit_date")
// 		->label("วันที่ถึงกำหนด*")
// 		->addClass("inputtext disabled")
// 		->autocomplete('off')
// 		->type('date')
// 		->attr("data-plugins", "datepicker")
// 		->attr("disabled", 1)
// 		->value( !empty($this->item['bill_submit_date']) ? $this->item['bill_submit_date'] : '' );

// $form 	->field("bill_address")
// 		->label("ที่อยู่")
// 		->addClass("inputtext")
// 		->autocomplete('off')
// 		->type('textarea')
// 		->attr('data-plugins', 'autosize')
// 		->value( '' );

$formTable = new Form();
$formTable = $formTable->create()
                       ->elem('div');

$table = '<table class="table-bordered" width="100%">
				<thead>
					<tr style="background-color: #0054A3; color:#fff;">
						<th width="5%">#</th>
						<th width="35%">สินค้า</th>
						<th width="10%">จำนวน</th>
						<th width="10%">หน่วย</th>
						<th width="10%">ราคา/หน่วย</th>
						<th width="10%">รวม</th>
						<th width="10%">หมายเหตุ</th>
						<th width="5%"></th>
					</tr>
				</thead>
				<tbody role="listsitem">
				</tbody>
			</table>';

$formTable->field('lists')
          ->text($table);

$options = $this->fn->stringify(array(
        'cus_id' => !empty($this->item['cus_id']) ? $this->item['cus_id'] : '',
        'items' => !empty($this->item['items']) ? $this->item['items'] : array(),
        'products' => $this->products,
));
?>
<div id="mainContainer" class="clearfix" data-plugins="main">
	<div class="clearfix">
		<div class="span12">
			<div role="toolbar" class="mtm">
				<h3 class="fwb"><i class="icon-university"></i> VAT Sale en Create</h3>
			</div>
			<div role="main">
				<form class="js-submit-form" action="<?=URL; ?>bills/save" data-plugins="billForm" data-options="<?=$options; ?>">
					<div class="uiBoxWhite pal mtm">
						<h3 class="fwb mbs"><i class="icon-home"></i> ข้อมูลร้านค้า</h3>
						<?=$form->html(); ?>
					</div>
					<div class="uiBoxWhite pal mtm form-insert">
						<h3 class="fwb mbs"><i class="icon-list"></i> รายการสั่งซื้อ</h3>
						<?=$formTable->html(); ?>
					</div>
					<div class="uiBoxWhite pal mts">
						<div class="clearfix">
							<div class="rfloat">
								<table>
									<tr>
										<td width="50%" class="fwb">สินค้าทั้งหมด</td>
										<td width="50%" class="tar"><span summary="item"></span></td>
									</tr>
									<tr>
										<td width="50%" class="fwb">รวมเป็นเงิน</td>
										<td width="50%" class="tar"><span summary="total"></span></td>
									</tr>
									<tr>
										<td width="50%" class="fwb">ภาษีมูลค่าเพิ่ม
											<input class="inputtext tac" readonly="1" name="vat" value="7" style="display:inline; width: 30px;">%
										</td>
										<td width="50%" class="tar"><span summary="vat"></span></td>
									</tr>
									<tr>
										<td width="50%" class="fwb">รวมเป็นเงินทั้งสิน</td>
										<td width="50%" class="tar"><span summary="amount"></span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="uiBoxWhite pal mts">
						<div class="clearfix">
							<a href="<?=URL; ?>bills" class="btn btn-red">Cancel</a>
							<button type="submit" class="js-submit btn btn-blue rfloat">SAVE</button>
						</div>
					</div>
					<input type="hidden" name="bill_total" value="0">
					<input type="hidden" name="bill_vat" value="0">
					<input type="hidden" name="bill_amount" value="0">
					<?php if (!empty($this->item)) {
    echo '<input type="hidden" name="id" value="'.$this->item['id'].'">';
}?>
				</form>
			</div>
		</div>
	</div>
</div>