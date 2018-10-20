<h3 class="fwb"><i class="icon-line-chart mrs"></i>ยอดขาย</h3>
<table class="table-bordered" width="100%">
	<thead>
		<tr style="background-color: #2f2f6f; color:#fff;">
			<th width="5%" class="pam">#</th>
			<th width="15%">Date</th>
			<th width="15%">ORDER CODE</th>
			<th width="50%">Shop name</th>
			<th width="15%">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$num = 0;
		$total_price = 0;
		if ( !empty($this->orders['lists']) ) {
			foreach ($this->orders['lists'] as $key => $value) {
				$num++;
				$total_price += $value['net_price'];
				?>
				<tr>
					<td class="pam tac"><?=$num?></td>
					<td class="pam tac"><?= date('d/m/Y', strtotime($value['date'])) ?></td>
					<td class="tac pam fwb"><a href="<?=URL?>payments/<?=$value["id"]?>"><?=$value['code']?></a></td>
					<td class="pam">
						<span class="fwb">
							<a href="<?=URL?>customers/<?=$value['customer_id']?>" target="_blank"><?=$value['user_name']?></a>
						</span>
					</td>
					<td class="tac pam"><?=number_format($value['net_price'],2)?></td>
				</tr>
				<?php
			}
		}else{
			echo '<td colspan="5" style="text-align:center; color:red;" class="fwb">No purchase information found.</td>';
		}
		?>
	</tbody>
	<tfoot>
		<th colspan="4" style="text-align: right;" class="fwb">Total</th>
		<th class="fwb" style="text-align: center;"><?=number_format($total_price, 2)?></th>
	</tfoot>
</table>