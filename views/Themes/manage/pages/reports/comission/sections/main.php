<div class="mhl pbl"><div class="uiBoxWhite pam">
	<h3><i class="icon-users mrs"></i> Saller name (<?=$this->period; ?>)</h3>
	<div ref="table" class="listpage2-table">
		<table class="table-bordered mtm">
			<thead>
				<tr>
					<th class="ID">Code</th>
					<th class="name">Saller name</th>
					<th class="status">Due</th>
					<th class="status">Orders</th>
					<th class="status_str">Commission</th>
				</tr>
			</thead>
			<tbody>
				<?php
                $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('m');
                $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');
                $total = 0;
                foreach ($this->results as $key => $value) {
                    $total += $value['comission']; ?>
					<tr>
						<td class="ID"><?=$value['sale_code']; ?></td>
						<td class="name"><?=$value['sale_name']; ?></td>
						<td class="status">
							<span class="gbtn">
								<a href="<?=URL; ?>reports/showDue/<?=$value['id']; ?>" class="btn btn-orange btn-no-padding" data-plugins="dialog"><i class="icon-money"></i></a>
							</span>
						</td>
						<td class="status">
							<span class="gbtn">
								<a href="<?=URL; ?>reports/showComission/<?=$value['id']; ?>/?month=<?=$month; ?>&year=<?=$year; ?>" class="btn btn-no-padding btn-blue" data-plugins="dialog">
									<i class="icon-eye"></i>
								</a>
							</span>
						</td>
						<td class="status_str">
							<?= !empty($value['comission']) ? number_format($value['comission'], 2) : '-'; ?>
						</td>
					</tr>
					<?php
                }
                ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="4">
						<div class="tar">
							<span class="fwb" style="font-size:20px;">Total</span>
						</div>
					</th>
					<th>
						<div class="tac">
							<span class="fwb" style="font-size:20px;"><?=number_format($total, 2);?></span>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div></div>