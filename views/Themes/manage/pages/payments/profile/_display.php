<div id="mainContainer" class="profile clearfix" data-plugins="main">
	<div class="setting-content" role="content">
		<div class="setting-main" role="main">
			<div class="pas pam">
				<div class="setting-header clearfix">
					<div class="setting-title"><i class="icon-cube"></i> <?=$this->item['code']?></div>
				</div>
				<div class="clearfix">
					<div class="span6">
						<div class="uiBoxOverlay pam">
							<h3 class="mbm fwb"><i class="icon-user"></i> ข้อมูลลูกค้า</h3>
							<ul>
								<li>
									<label><span class="fwb">ชื่อร้าน : </span><?=$this->item['user_name']?></label>
								</li>
								<li>
									<label><span class="fwb">รหัส : </span><?=$this->item['user_code']?></label>
								</li>
								<li>
									<label><span class="fwb">เงื่อนไขการชำระเงิน : </span><?=$this->item['term_of_payment']['name']?></label>
								</li>
								<li>
									<label><span class="fwb">ยอดรวม : </span><?=number_format($this->item['prices'], 2)?> บาท</label>
								</li>
							</ul>
						</div>
					</div>
					<div class="span6">
						<div class="uiBoxWhite pas pam">
							<div class="rfloat">
								<a class="btn btn-blue" data-plugins="dialog" href="<?=URL?>payments/add/<?=$this->item['id']?>"><i class="icon-plus"></i> เพิ่ม</a>
							</div>
							<div class="nfloat mts">
								<h3 class="fwb mbm"><i class="icon-money"></i> รายการจ่ายเงิน</h3>
								<div ref="table" class="listpage2-table">
									<table class="table-bordered">
										<thead>
											<tr>
												<th class="ID">ลำดับ</th>
												<th class="date">วันที่</th>
												<th clsss="name">วิธีชำระ</th>
												<th class="price">จำนวนเงิน</th>
												<th class="status">หลักฐาน</th>
												<th class="actions">จัดการ</th>
											</tr>
										</thead>
										<?php 
											if( !empty($this->item['payment_lists']) ){
												$no = 0;
										?>
										<tbody>
											<?php
												foreach ($this->item['payment_lists'] as $key => $value) {
													$no++;

													$date = '<div>'.date("d/m/Y", strtotime($value['date'])).'</div>';
													$date .= '<div>'.date("H:i", strtotime($value['time'])).'</div>';

													$type = "";
													if( !empty($value['type_is_cash']) ){
														$type = $value['type_name'];
													}
													elseif( !empty($value['type_is_bank']) ){
														$type = "<div>".$value['type_name'].'</div>';
														$type .= "<div>(".$value['bank_code'].") ".$value['account_number']."</div>";
													}
													elseif( !empty($value['type_is_check']) ){
														$type = "<div>".$value['type_name'].'</div>';
														$type .= "<div>".$value['bank_code']."-".$value['check_number']."</div>";
													}

													$image = '-';
													if( !empty($value['image_arr']) ){
														$image = '<a href="'.URL.'payments/showPicture/'.$value['id'].'" target="_blank" data-plugins="dialog"><i class="icon-eye"></i></a>';
													}
													?>
													<tr>
														<td class="ID"><?=$no?></td>
														<td class="date" style="text-align: center;">
															<?=$date?>
														</td>
														<td class="name" style="text-align: center;">
															<?=$type?>
														</td>
														<td class="price">
															<?=number_format($value['amount'], 2)?>
														</td>
														<td class="status"><?=$image?></td>
														<td class="actions whitespace">
															<span class="gbtn">
																<a data-plugins="dialog" href="<?=URL?>payments/edit/<?=$value['id'];?>" class="btn btn btn-no-padding btn-orange"><i class="icon-pencil"></i>
																</a>
															</span>
															<span class="gbtn">
																<a data-plugins="dialog" href="<?=URL?>payments/del/<?=$value['id'];?>" class="btn btn btn-no-padding btn-red"><i class="icon-trash"></i>
																</a>
															</span>
														</td>
													</tr>
											    <?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="4" style="text-align: right;">ยอดรวม</th>
												<th colspan="2" style="text-align: center;"><?=number_format($this->item['prices'], 2)?></th>
											</tr>
											<tr>
												<th colspan="4" style="text-align: right;">ชำระแล้ว</th>
												<th colspan="2" style="text-align: center;"><?= !empty($this->item['pay']) ? number_format($this->item['pay'], 2) : "-" ?></th>
											</tr>
											<tr>
												<th colspan="4" style="text-align: right;">คงค้าง</th>
												<th colspan="2" style="text-align: center;"><?= !empty($this->item['balance']) ? number_format($this->item['balance'], 2) : "-" ?></th>
											</tr>
										</tfoot>
										<?php }
										else{
											echo '<tbody>
													<tr>
														<td colspan="6" style="text-align:center;">
															<span class="fwb" style="color:red;">ไม่พบข้อมูลการชำระเงิน</span>
														</td>
													</tr>
												</tbody>';
										} ?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div class="span12">
						<div class="uiBoxOverlay mtm pam">
							<h3 class="fwb mbm"><i class="icon-shopping-basket"></i> รายการสินค้า</h3>
							<div ref="table" class="listpage2-table">
								<table class="table-bordered">
									<thead>
										<tr>
											<th class="ID">ลำดับ</th>
											<th class="name">ชื่อสินค้า</th>
											<th class="qty">จำนวน</th>
											<th class="price">ราคา</th>
											<th class="price">ส่วนลด</th>
											<th class="price">ราคารวม</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$no = 0;
										$total_price = 0;
										foreach ($this->item['items'] as $key => $item) { 
											$no++;
											$cls = $i%2 ? 'even' : "odd";
											$total_price += $item['price'] * $item['qty'];
										?>
											<tr class="<?=$cls?>">
												<td class="ID"><?=$no?></td>
												<td class="name"><?=$item['name']?></td>
												<td class="qty" style="text-align: center;"><?=$item['qty']?></td>
												<td class="price"><?=number_format($item['price'], 2)?></td>
												<td class="price"><?= $item['discount'] != 0.00 ? number_format($item['discount'], 2) : "-" ?></td>
												<td class="price"><?=number_format($item['balance'], 2)?></td>
											</tr>
										<?php } ?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="2" style="text-align: right;">รวม</th>
											<th style="text-align: center;"><?=$this->item['total_qty']?></th>
											<th style="text-align: center;"><?=number_format($total_price, 2)?></th>
											<th style="text-align: center;"><?= !empty($this->item['total_discount']) ? number_format($this->item['total_discount'], 2) : "-" ?></th>
											<th style="text-align: center;"><?=number_format($this->item['prices'], 2)?></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<div class="uiBoxOverlay mtm pam mbl">
							<h3 class="fwb mbm"><i class="icon-shopping-cart"></i> รายการที่เกี่ยวข้อง</h3>
							<div ref="table" class="listpage2-table">
								<table class="table-bordered">
									<thead>
										<tr>
											<th class="ID">ลำดับ</th>
											<th class="name">เลข ORDER</th>
											<th class="price">ราคารวม</th>
											<th class="price">ชำระแล้ว</th>
											<th class="price">ยอดคงค้าง</th>
										</tr>
									</thead>
									<?php if( !empty($this->orders['lists']) ) { ?>
									<tbody>
										<?php 
											$no=0;
											$prices = 0;
											$pay = 0;
											$balance = 0;
											foreach ($this->orders['lists'] as $key => $value) { 
											$no++;
											$cls = $i%2 ? 'even' : "odd";

											$prices += $value['prices'];
											$pay += $value['pay'];
											$balance += $value['balance'];
										?>
											<tr>
												<td class="ID"><?=$no;?></td>
												<td class="name fwb">
													<a href="<?=URL?>payments/<?=$value['id']?>" target="_blank"><?=$value['code']?></a>
												</td>
												<td class="price" style="text-align: center;">
													<?=number_format($value['prices'], 2)?>
												</td>
												<td class="price" style="text-align: center;">
													<?=number_format($value['pay'], 2)?>
												</td>
												<td class="price" style="text-align: center;" >
													<?=number_format($value['balance'], 2)?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="2" style="text-anchor: right;" class="fwb">รวม</td>
											<td class="price fwb"><?=number_format($prices, 2)?></td>
											<td class="price fwb"><?=number_format($pay, 2)?></td>
											<td class="price fwb"><?=number_format($balance, 2)?></td>
										</tr>
									</tfoot>
									<?php }else{ ?>
									<tbody>
										<tr>
											<td colspan="5" style="text-align: center; color:red" class="fwb">ไม่พบรายการเกี่ยวข้อง / ลูกค้าได้สั่งเพียง 1 รายการ</td>
										</tr>
									</tbody>
									<?php } ?>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>