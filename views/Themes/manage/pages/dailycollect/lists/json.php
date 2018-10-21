<?php 
$total = 0;

?>
<table class="table-bordered">
	<thead>
		<tr style="background-color: blue; color:#fff;">
			<th class="pas" width="10px">#</th>
			<th style="width:50px;">Bill No</th>
			<th style="width:130px;">Delivery</th>
			<th style="width:130px;">Due</th>
			<th style="width:50px;">Sale</th>
			<th style="width:250px;">Customer</th>
			<th style="width:100px;">Expected Amount</th>
			<th style="width:100px;">Actual Amount</th>
			<th style="width:100px;">Collector</th>
			<th style="width:100px;">Remark</th>
			
		</tr>
	</thead>
	<tbody>
		<?php 
		if( !empty($this->results) ) {
			
			$i=1;
		foreach ($this->results as $key => $value) { ?>
		<tr>
			<td class="tac pas"><?=$i++;?></td>
			<td width="20px" class="tac pas"><?=$value["order_id"]?></td>
			<td class="pas tac"><?=$this->fn->q('time')->full(strtotime($value['delivery_date']),false,true,false)?></td>
			<td class="tac pas"><?=$this->fn->q('time')->full(strtotime($value['due_date']),false,true,false)?></td>
			<td class="tac pas">(<?=$value['sale']?>)</td>
			<td class="tac pas"><?=$value["user_name"]?></td>
			<td style="width:100px;" class="tac pas"><?=number_format($value["expected_amount"])?> บาท</td>
			<td style="width:100px;"><input style="over-flow:hidden; width:100px;" class="tar js-auto-submit" data-target="<?=$value['order_id']?>"></td>
			<td style="width:100px;" class="tar"><input class="tar" data-target="<?=$value['order_id']?>"></td>
			<td style="width:100px;" class="tar"><input class="tar" data-target="<?=$value['order_id']?>"></td>
		</tr>
		<?php $total += $value["expected_amount"]; } 
		}else{
			echo '<tr><td colspan="5" class="pam tac fcr fwb">Not found !</td></tr>';
		} ?>
	</tbody>
	<tfoot>
		<tr>
			<th class="pas tar" colspan="6">TOTAL</th>
			<th class="pas tar"><?=$total?></th>
			<th class="pas tar"><?=$total?></th>
			<th class="pas tar" colspan="3"></th>
		</tr>
	</tfoot>
</table>