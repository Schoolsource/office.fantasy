<?php
$form = new Form();
$form = $form->create()
			 ->elem('div')
			 ->addClass('form-insert');

$form 	->field("exp_date")
		->label("DATE")
		->attr('data-plugins', 'datepicker')
		->type('date')
		->addClass("inputtext")
		->autocomplete("off")
		->value( !empty($this->item['date']) ? $this->item['date'] : '' );

$form 	->field("exp_cate_id")
		->label("Category")
		->addClass('inputtext')
		->autocomplete('off')
		->select( $this->category )
		->value( !empty($this->item['cate_id']) ? $this->item['cate_id'] : '' );

$form 	->field("exp_ref")
		->label("Ref Number")
		->addClass('inputtext')
		->autocomplete('off')
		->value( !empty($this->item['ref']) ? $this->item['ref'] : '' );


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

$formTable	->field("lists")
			->text( $table );

$options = $this->fn->stringify( array(
		'items' => !empty($this->item['items']) ? $this->item['items'] : array(),
		'products' => $this->products
) );
?>
<div id="mainContainer" class="clearfix" data-plugins="main">
	<div class="clearfix">
		<div class="span12">
			<div role="toolbar" class="mtm">
				<h3 class="fwb"><i class="icon-upload mrs"></i> Stock Adjust</h3>
			</div>
			<div role="main">
				<form class="js-submit-form" action="<?=URL?>export/save" data-plugins="exportForm" data-options="<?=$options?>">
					<div class="uiBoxWhite pal mtm">
						<h3 class="fwb mbs"><i class="icon-home"></i> ข้อมูล</h3>
						<?=$form->html()?>
					</div>
					<div class="uiBoxWhite pal mtm form-insert">
						<h3 class="fwb mbs"><i class="icon-list"></i> รายการสินค้า</h3>
						<?=$formTable->html()?>
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
										<td width="50%" class="fwb">รวมเป็นเงินทั้งสิน</td>
										<td width="50%" class="tar"><span summary="total"></span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="uiBoxWhite pal mts">
						<div class="clearfix">
							<a href="<?=URL?>export" class="btn btn-red">Cancel</a>
							<button type="submit" class="js-submit btn btn-blue rfloat">SAVE</button>
						</div>
					</div>
					<input type="hidden" name="exp_total_price" value="0">
					<?php if( !empty($this->item) ) { 
						echo '<input type="hidden" name="id" value="'.$this->item['id'].'">';
					}?>
				</form>
			</div>
		</div>
	</div>
</div>