<?php
$address = '';
if( !empty($this->item['address']) ){
	$address .= $this->item['address'];
}
if( !empty($this->item['street']) ){
	$address .= ' <span class="fwb">Road </span>'.$this->item['street'];
}
if( !empty($this->item['supdistrict']) ){
	$address .= ' <span class="fwb">District </span>'.$this->item['supdistrict'];
}
if( !empty($this->item['district']) ){
	$address .= ' <span class="fwb">Area </span>'.$this->item['district'];
}
if( !empty($this->item['province_name']) ){
	$address .= ' <span class="fwb">Province </span>'.$this->item['province_name'];
}
if( !empty($this->item['country_name']) ){
	$address .= ' <span class="fwb">'.$this->item['country_name'].'</span>';
}

$total = 0;
?>
<div id="mainContainer" class="profile clearfix" data-plugins="main">
	<div class="setting-content" role="content">
		<div class="setting-main" role="main">
			<div class="clearfix pam">
				<div class="span12">
					<div class="setting-title">
						<i class="icon-handshake-o mrm"></i><?=$this->item['name']?>
					</div>
					<div class="rfloat mrm">
						<a class="btn btn-no-padding btn-orange" data-plugins="dialog" href="<?=URL?>suppliers/edit/<?=$this->item['id']?>"><i class="icon-pencil"></i></a>

						<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="<?=URL?>suppliers/del/<?=$this->item['id']?>?next=<?=URL?>suppliers"><i class="icon-trash"></i></a>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div class="span12">
					<div class="uiBoxOverlay pam pas">
						<h3 class="mbm fwb"><i class="icon-user mrs"></i> Information</h3>
						<ul>
							<li>
								<label><span class="fwb">Supplier Name : </span><?=$this->item['name']?></label>
							</li>
							<li>
								<label><span class="fwb">Contact Name : </span><?=$this->item['sup_contact']?></label>
							</li>
							<li>
								<label><span class="fwb">Address : </span><?= !empty($address) ? $address : "-" ?></label>
							</li>
							<li>
								<label><span class="fwb">Mobile Phone : </span><?= !empty($this->item['mobile_phone']) ? $this->item['mobile_phone'] : "-" ?></label>
							</li>
							<li>
								<label><span class="fwb">Phone : </span><?= !empty($this->item['phone']) ? $this->item['phone'] : "-" ?></label>
							</li>
							<li>
								<label><span class="fwb">FAX : </span><?= !empty($this->item['fax']) ? $this->item['fax'] : "-" ?></label>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="clearfix mtm">
				<div class="span6">
					<div class="rfloat pam">
						<span class="gbtn">
							<a href="<?=URL?>paycheck/add?sup=<?=$this->item['id']?>" class="btn btn-no-padding btn-blue" data-plugins="dialog"><i class="icon-plus"></i></a>
						</span>
					</div>
					<div class="uiBoxOverlay pam pas">
						<h3 class="mbm fwb"><i class="icon-cc-visa mrs"></i> Check Lists</h3>
						<div ref="table" class="listpage2-table">
							<table class="table-bordered">
								<thead>
									<tr>
										<th class="ID">ลำดับ</th>
										<th class="date">วันที่</th>
										<th class="name">เลขที่เช็ค</th>
										<th class="contact">ธนาคาร</th>
										<th class="price">จำนวนเงิน</th>
										<th class="status">หลักฐาน</th>
										<th class="actions"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									if( !empty($this->item['check']) ) {
										$no=0;
										foreach ($this->item['check'] as $key => $value) {
											$no++;
											$total += $value['price'];
											?>
											<tr>
												<td class="ID"><?=$no?></td>
												<td class="date">
													<?=date("d/m/Y", strtotime($value['date']))?>
												</td>
												<td class="name"><?=$value['number']?></td>
												<td class="contact"><?=$value['bank_name']?> (<?=$value['bank_code']?>)</td>
												<td class="price"><?=number_format($value['price'],2)?></td>
												<td class="status">
													<?php if( !empty($value['image_id']) ) {
														echo '<span class="gbtn"><a href="'.URL.'paycheck/showPicture/'.$value['id'].'" data-plugins="dialog" class="btn btn-blue btn-no-padding"><i class="icon-eye"></i></a></span>';
													}else{
														echo "-";
													}?>
												</td>
												<td class="actions">
													<div class="group-btn whitespace">
														<span class="gbtn">
															<a href="<?=URL?>paycheck/edit/<?=$value['id']?>" data-plugins="dialog" class="btn btn-no-padding btn-orange"><i class="icon-pencil"></i></a>
														</span>
														<span class="gbtn">
															<a href="<?=URL?>paycheck/del/<?=$value['id']?>" data-plugins="dialog" class="btn btn-no-padding btn-red"><i class="icon-trash"></i></a>
														</span>
													</div>
												</td>
											</tr>
											<?php
										}
									}
									else{
										echo '<tr><td colspan="7" style="text-align:center; color:red;" class="fwb"><i class="icon-exclamation-circle"></i> ไม่พบข้อมูล</td></tr>';
									} ?>
								</tbody>
								<tfoot>
									<th colspan="4" style="text-align: right; font-size: 20px;">รวม</th>
									<th colspan="3" style="text-align: center; font-size: 20px;"><?=number_format($total,2)?></th>
								</tfoot>
							</table>
						</div>
					</div>
				</div>

				<div class="span6">
					<div class="rfloat pam">
						<span class="gbtn">
							<a href="<?=URL?>events/add?obj_id=<?=$this->item['id']?>&obj_type=suppliers" class="btn btn-no-padding btn-blue" data-plugins="dialog"><i class="icon-plus"></i></a>
						</span>
					</div>
					<div class="uiBoxOverlay pam pas">
						<h3 class="mbm fwb"><i class="icon-calendar-check-o mrs"></i> นัดหมาย</h3>
						<div ref="table" class="listpage2-table">
							<table class="table-bordered">
								<thead>
									<tr>
										<th class="ID">No.</th>
										<th class="date">วันที่</th>
										<th class="name">หัวข้อ</th>
										<th class="actions"></th>
									</tr>
								</thead>
								<tbody>
									<?php if( !empty($this->events['lists']) ){
										$no=0;
										foreach ($this->events['lists'] as $key => $value) { $no++;
											$time = 'ทั้งวัน';
											$start_time = date("H:i", strtotime($value['start']));
											if( $start_time != '00:00') {
												$time = $start_time;
											}
											?>
											<tr>
												<td class="ID"><?=$no?></td>
												<td class="date">
													<?=date("d/m/Y", strtotime($value['start']))?>
													(<?=$time?>)
												</td>
												<td class="name"><?=$value['title']?></td>
												<td class="actions whitespace">
													<span class="gbtn">
														<a href="<?=URL?>events/edit/<?=$value['id']?>" class="btn btn-no-padding btn-orange" data-plugins="dialog"><i class="icon-pencil"></i></a>
													</span>
													<span class="gbtn">
														<a href="<?=URL?>events/del/<?=$value['id']?>" class="btn btn-no-padding btn-red" data-plugins="dialog"><i class="icon-trash"></i></a>
													</span>
												</td>
											</tr>
											<?php
										}
									}
									else{
										echo '<tr>
												<td colspan="5" style="text-align:center;">
													<span class="fwb" style="color:red;">ไม่พบข้อมูลนัดหมาย</span>
												</td>
											</tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
