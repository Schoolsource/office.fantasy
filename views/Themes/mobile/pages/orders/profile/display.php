<div class="web-profile">

	<div class="web-profile-header">
		<h1 class="fwb"><i class="icon-cube"></i> <?=$this->item['code']?></h1>
	</div>

	<div class="web-profile-content post">
		
		<table class="table-meta">
			<?php 
			$a = array();
			$a[] = array('key'=>'user_code', 'icon'=>'address-book-o', 'label'=>'รหัส');
			$a[] = array('key'=>'user_name', 'icon'=>'user', 'label'=>'ชื่อร้านค้า');
			$a[] = array('key'=>'net_price', 'icon'=>'money', 'label'=>'ราคารวม');

			foreach ($a as $key => $value) {
				if( $value['key']=='net_price' ) {
					$this->item[$value['key']] = number_format($this->item[$value['key']], 2);
				}
				echo '<tr>
						<td class="label">
							<i class="icon-'.$value['icon'].'"></i> '.$value['label'].'
						</td>
						<td class="fwb">'.$this->item[$value['key']].'</td>
					  </tr>';
			}
			?>
		</table>

		<div class="web-profile-header">
			<h1>รายการสินค้า</h1>
			<table class="table table-bordered mtl" width="100%">
				<thead>
					<tr>
						<th class="name" width="55%">สินค้า</th>
						<th class="price" style="color:green" width="15%">จำนวน</th>
						<th class="price" width="15%">ส่วนลด</th>
						<th class="price" style="color:red" width="15%">เงิน</th>
					</tr>
				</thead>
				<tbody>
					<?php $num=0; foreach ($this->item['items'] as $key => $value) { $num++ ?>
						<tr>
							<td><?=$num?>. <?=$value['name']?></td>
							<td class="tac"><?=number_format($value['price'],2)?></td>
							<td class="tac"><?=number_format($value['discount'],2)?></td>
							<td class="tac"><?=number_format($value['balance'],2)?></td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="tac fwb">ยอดรวมเงิน <?=$num?> รายการ</td>
						<td colspan="3" class="tac fwb"><?=$this->item['net_price']?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>