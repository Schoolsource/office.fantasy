<?php 
$total = 0;
$vat = 0;
$amount = 0;
?>
<div class="clearfix">
	<?php if( !empty($this->results["lists"]) ) { ?>
	<div class="rfloat mbm">
		<a href="<?=URL?>/pdf/listsVatsale/?period_start=<?=$_GET["period_start"]?>&period_end=<?=$_GET["period_end"]?>&term_of_payment=<?=$_GET["term_of_payment"]; ?>" class="btn btn-red" target="_blank"><i class="icon-file-pdf-o mrs"></i>PDF</a>
	</div>
	<div class="lfloat">
		<h3 class="fwb"><i class="icon-list"></i> รายงาน</h3>
	</div>
	<table width="100%" class="table-bordered">
		<thead>
			<tr style="background-color:Violet;">
				<th width="10%">เลขที่ใบส่งของ</th>
				<th width="10%">วันที่ส่งของ</th>
				<th width="10%">รหัส</th>
				<th width="20%">ชื่อร้าน</th>
				<th width="15%">ยอดรวม</th>
				<th width="15%">VAT</th>
				<th width="15%">ยอดสุทธิ</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($this->results["lists"] as $key => $value) {
				echo '<tr>
						<td align="center">IN'.sprintf("%05d", $value["id"]).'</td>
						<td align="center">'.date("d/m/Y", strtotime($value["send_date"])).'</td>
						<td align="center">'.$value["sub_code"].'</td>
						<td style="padding-left:1mm;">'.$value["name_store"].'</td>
						<td align="right">'.number_format($value["total"], 2).'</td>
						<td align="right">'.number_format($value["vat"], 2).'</td>
						<td align="right">'.number_format($value["amount"], 2).'</td>
					</tr>';

				$total += $value["total"];
				$vat += $value["vat"];
				$amount += $value["amount"];
			}
			?>
		</tbody>
		<tfoot>
			<?php echo '<tr style="background-color:Violet;">
					<th colspan="4">รวม</th>
					<th align="right">'.number_format($total, 2).'</th>
					<th align="right">'.number_format($vat, 2).'</th>
					<th align="right">'.number_format($amount, 2).'</th>
				</tr>'
			?>
		</tfoot>
	</table>
	<?php 
	}
	else{
		echo '<h3 class="tac fcr fwb">ไม่พบข้อมูล</h3>';
	} ?>
</div>