<?php 
$total = 0;
?>
<table class="table-bordered">
	<thead>
		<tr style="background-color: blue; color:#fff;">
			<th class="pas" width="10px">#</th>
			<th style="width:50px;">Bill No</th>
			<th style="width:130px;">Delivery</th>
			<th style="width:50px;">Sale</th>
			<th style="width:200px;">Customer</th>
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
			<td width="20px" class="tac pas"><?=$value["ord_code"]?></td>
			<td class="pas tac"><?=$this->fn->q('time')->full(strtotime($value['ord_dateCreate']),false,true,false)?></td>
			<td class="tac pas">(<?=$value["ord_sale_code"]?>)</td>
			<td class="tac pas"><?=$value["user_name"]?></td>
			<td class="tar pas"><?= number_format($value["balance"]) ?> บาท</td>
		</tr>
		<?php $total += $value["balance"]; } 
		}else{
			echo '<tr><td colspan="5" class="pam tac fcr fwb">Not found !</td></tr>';
		} ?>
	</tbody>
	<tfoot>
		<tr>
			<th class="pas tar" colspan="4">TOTAL</th>
			<th class="pas tar"><?= number_format($total) ?></th>
		</tr>
	</tfoot>
</table>