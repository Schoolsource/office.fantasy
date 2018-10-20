<?php 
$total = 0;
$vat = 0;
?>
<div class="clearfix">
	<?php if( !empty($this->results["lists"]) ) { ?>
	<div class="rfloat mbm">
		<a href="<?=URL?>/pdf/vat_buy/?period_start=<?=$_GET["period_start"]?>&period_end=<?=$_GET["period_end"]?>" class="btn btn-red" target="_blank"><i class="icon-file-pdf-o mrs"></i>PDF</a>
	</div>
	<div class="lfloat">
		<h3 class="fwb"><i class="icon-list"></i> รายงาน</h3>
	</div>
	<table width="100%" class="table-bordered">
		<thead>
			<tr style="background-color:Violet;">
				<th width="15%" style="font-weight: bold;">Slipt</th>
				<th width="10%" style="font-weight: bold;">Date</th>
				<th width="30%" style="font-weight: bold;">Name</th>
				<th width="10%" style="font-weight: bold;">Total</th>
				<th width="10%" style="font-weight: bold;">Vat</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->results["lists"] as $key => $value) { ?>
			<tr>
				<td><?=$value["slipt"]?></td>
				<td align="center"><?=date("d/m/Y", strtotime($value["date"]))?></td>
				<td><?=$value["sup_name"]?></td>
				<td align="right"><?=number_format($value["total"], 2)?></td>
				<td align="right"><?=number_format($value["vat"], 2)?></td>
			</tr>
			<?php 
			$total += $value["total"];
			$vat += $value["vat"]; } 
			?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="3">รวม</th>
				<th class="tar"><?=number_format($total, 2)?></th>
				<th class="tar"><?=number_format($vat, 2)?></th>
			</tr>
		</tfoot>
	</table>
	<?php 
	}
	else{
		echo '<h3 class="tac fcr fwb">ไม่พบข้อมูล</h3>';
	} ?>
</div>