<?php 
$total = 0;
?>
<table class="table-bordered" width="100%">
	<thead>
		<tr style="background-color: blue; color:#fff;">
			<th class="pas" width="10%">Due Date</th>
			<th width="10%">Order Number</th>
			<th width="50%">Shop Name</th>
			<th width="20%">Sale Name</th>
			<th width="10%">Amount</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if( !empty($this->results) ) {
		foreach ($this->results as $key => $value) { ?>
		<tr>
			<td class="tac pas"><?=date("d/m/Y", strtotime($value["ord_dateCreate"]))?></td>
			<td class="tac pas"><?=$value["ord_code"]?></td>
			<td class="pas">(<?=$value["user_code"]?>) <?=$value["user_name"]?></td>
			<td class="tac pas">(<?=$value["ord_sale_code"]?>) <?=$value["sale_fullname"]?></td>
			<td class="tar pas"><?= number_format($value["balance"]) ?></td>
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