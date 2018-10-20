<div class="clearfix">
	<h3 class="fwb"><i class="icon-list-alt"></i> Order ( of day <?=$this->periodStr; ?>)</h3>
	<a href="<?=URL; ?>pdf/reports/revenue?period_start=<?=$this->start; ?>&period_end=<?=$this->end; ?>&sale=<?=$this->sale; ?>&term_of_payment=<?=$this->term; ?>" class="btn btn-blue rfloat mbm" target="_blank"><i class="icon-print"></i> PRINT</a>
	<div ref="table" class="listpage2-table">
		<table class="table-bordered mtm">
			<thead>
				<tr>
					<th class="ID">Order</th>
					<th class="date">Date</th>
					<th style="width: 120px;">Order number</th>
					<th style="width: 100px;">Term</th>
					<th>Store Name / Order Name</th>
					<th class="price">Total price</th>
				</tr>
			</thead>
			<tbody>
				<?php
                if (!empty($this->results['lists'])) {
                    $num = 1;
                    foreach ($this->results['lists'] as $key => $value) {
                        ?>
						<tr>
							<td class="ID"><?=$num; ?></td>
							<td class="date"><?=date('d/m/Y', strtotime($value['date'])); ?></td>
							<td><?=$value['code']; ?></td>
							<td><?=str_replace('เครดิต', '', $value['term_of_payment']['name']); ?></td>
							<td><?='['.$value['user_code'].'] '.$value['user_name']; ?></td>
							<td class="price"><?=number_format($value['net_price'], 2); ?></td>
						</tr>
						<?php
                        ++$num;
                    }
                }
                ?>
			</tbody>
		</table>
	</div>
</div>
