<h3 class="fwb"><i class="icon-money mrs"></i>ยอดเก็บเงิน | <?= number_format($this->results["total_amount"], 2) ?> ฿</h3>
<table class="table-bordered" width="100%">
	<thead>
		<tr style="background-color: green; color:#fff;">
			<th width="15%" class="pam">Date Payment</th>
			<th width="20%">Order Code</th>
			<th width="15%">Customer Code</th>
			<th width="40%">Customer Name</th>
			<th width="20%">Amount</th>
		</tr>
	</thead>
	<tbody>
		<?php if( !empty($this->results['lists']) ) { 
			foreach ($this->results['lists'] as $key => $value) {
				// print_r($value);die;
				?>
				<tr>
					<td class="tac pam"><?= date("d/m/Y", strtotime($value["pay_date"])) ?></td>
					<td class="tac pam">
						<a class="fwb" target="_blank" href="<?=URL?>payments/<?=$value["pay_order_id"]?>">
							<?= $value["ord_code"] ?>
						</a>
					</td>
					<td class="tac pam"><?= $value["sub_code"] ?></td>
					<td class="pam">
						<a class="fwb" target="_blank" href="<?=URL?>customers/<?=$value["cus_id"]?>">
							<?= $value["name_store"] ?>
						</a>
					</td>
					<td class="tac pam"><?= number_format($value["pay_amount"],2) ?></td>
				</tr>
				<?php 
			}
		}
		else{
			echo '<tr><td colspan="5" class="tac fcr fwb">No payment information found.</td></tr>';
		} ?>
	</tbody>
	<tfoot>
		<th colspan="4" class="fwb tar pam">Total</th>
		<th class="fwb" class="tac pam"><?=number_format($this->results["total_amount"], 2)?></th>
	</tfoot>
</table>