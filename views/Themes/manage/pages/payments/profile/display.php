<div id="mainContainer" class="profile clearfix" data-plugins="main">
	<div class="setting-content" role="content">
		<div class="setting-main" role="main">
			<div class="pas pam">
				<div class="setting-header clearfix">
					<div class="setting-title"><i class="icon-cube"></i> <?=$this->item['code']; ?></div>
				</div>
				<div class="clearfix">
					<div class="span6">
						<div class="uiBoxOverlay pam">
							<h3 class="mbm fwb"><i class="icon-user"></i> Customer Infomation</h3>
							<ul>
								<li>
									<label><span class="fwb">Shop name : </span><?=$this->item['user_name']; ?></label>
								</li>
								<li>
									<label><span class="fwb">Code : </span><?=$this->item['user_code']; ?></label>
								</li>
								<li>
									<label><span class="fwb">Term of payment : </span><?= !empty($this->item['term_of_payment']) ? $this->item['term_of_payment']['name'] : '-'; ?></label>
								</li>
								<li>
									<label><span class="fwb">Total : </span><?=number_format($this->item['net_price'], 2); ?> Bath</label>
								</li>
								<?php if ($this->item['process']['id'] == 7) {
    ?>
								<li>
									<label><span class="fwb">Status : </span> <a class="btn btn-red">ยกเลิก</a></label>
								</li>
								<?php
} ?>

								<li style="display: none;">
									<label style="display: inline-block;"><span class="fwb">Project: </span> </label>

									<select style="display: inline-block;" name="project" class="inputtext" data-name="project_id" data-id="<?=$this->item['id']; ?>">
										<option value="">-</option>
										<?php
                                        $projectName = '-';
                                        foreach ($this->projectList as $key => $value) {
                                            $active = $value['project_id'] == $this->item['project_id'] ? ' selected' : '';
                                            $disabled = !empty($value['project_enabled']) ? '' : ' disabled';
                                            if (!empty($active) && !empty($disabled)) {
                                                $disabled = '';
                                            }
                                            if (!empty($disabled)) {
                                                continue;
                                            }
                                            if ($value['project_id'] == $this->item['project_id']) {
                                                $projectName = $value['project_name'];
                                            }

                                            echo '<option'.$active.$disabled.' value="'.$value['project_id'].'">'.$value['project_name'].'</option>';
                                        }
                                        ?>
									</select>
								</li>
								<li>
								<label style="display: inline-block;"><span class="fwb">Project: </span> <?php echo $projectName; ?></label>
								</li>
							</ul>
						</div>

						<div class="uiBoxOverlay mtm pam">
							<h3 class="fwb mbm"><i class="icon-shopping-basket"></i> Item</h3>
								<table class="table-bordered" width="100%">
									<thead>
										<tr>
											<th width="10%">#</th>
											<th width="35%">Product name</th>
											<th width="10%">Number</th>
											<th width="15%"> Price</th>
											<th width="15%"> discount</th>
											<th width="15%"> Total price</th>
										</tr>
									</thead>
									<tbody>
										<?php
                                        $no = 0;
                                        $total_price = 0;
                                        foreach ($this->item['items'] as $key => $item) {
                                            ++$no;
                                            $cls = $i % 2 ? 'even' : 'odd';
                                            $total_price += $item['price'] * $item['qty']; ?>
											<tr class="<?=$cls; ?>">
												<td class="tac pas"><?=$no; ?></td>
												<td class="name pas"><?=$item['name']; ?></td>
												<td class="tac pas"><?=$item['qty']; ?></td>
												<td class="tar pas"><?=number_format($item['price'], 2); ?></td>
												<td class="tar pas"><?= $item['discount'] != 0.00 ? number_format($item['discount'], 2) : '-'; ?></td>
												<td class="tar pas"><?=number_format($item['balance'], 2); ?></td>
											</tr>
										<?php
                                        } ?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="2" class="tar">Total</th>
											<th class="tac"><?=$this->item['total_qty']; ?></th>
											<th class="tac"><?=number_format($total_price, 2); ?></th>
											<th class="tac"><?= !empty($this->item['total_discount']) ? number_format($this->item['total_discount'], 2) : '-'; ?></th>
											<th class="tac"><?=number_format($this->item['prices'], 2); ?></th>
										</tr>
									</tfoot>
								</table>
						</div>

						<div class="uiBoxOverlay mtm pam mbl">
							<h3 class="fwb mbm"><i class="icon-shopping-cart"></i> Related Items</h3>
								<table class="table-bordered" width="100%">
									<thead>
										<tr>
											<th width="10%" class="pas">#</th>
											<th width="30%">Order number</th>
											<th width="20%">Total price</th>
											<th width="20%">Paid</th>
											<th width="20%">Outstanding</th>
										</tr>
									</thead>
									<?php if (!empty($this->orders['lists'])) {
                                            ?>
									<tbody>
										<?php
                                            $no = 0;
                                            $prices = 0;
                                            $pay = 0;
                                            $balance = 0;
                                            foreach ($this->orders['lists'] as $key => $value) {
                                                ++$no;
                                                $cls = $i % 2 ? 'even' : 'odd';

                                                $prices += $value['prices'];
                                                $pay += $value['pay'];
                                                $balance += $value['balance']; ?>
											<tr>
												<td class="tac pas"><?=$no; ?></td>
												<td class="pas fwb">
													<a href="<?=URL; ?>payments/<?=$value['id']; ?>" target="_blank"><?=$value['code']; ?></a>
												</td>
												<td class="tac pas">
													<?=number_format($value['prices'], 2); ?>
												</td>
												<td class="tac pas">
													<?=number_format($value['pay'], 2); ?>
												</td>
												<td class="tac pas" >
													<?=number_format($value['balance'], 2); ?>
												</td>
											</tr>
										<?php
                                            } ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="2" style="text-anchor: right;" class="fwb">รวม</td>
											<td class="price fwb"><?=number_format($prices, 2); ?></td>
											<td class="price fwb"><?=number_format($pay, 2); ?></td>
											<td class="price fwb"><?=number_format($balance, 2); ?></td>
										</tr>
									</tfoot>
									<?php
                                        } else {
                                            ?>
									<tbody>
										<tr>
											<td colspan="5" style="text-align: center; color:red" class="fwb">No related items / customers have only 1 order.</td>
										</tr>
									</tbody>
									<?php
                                        } ?>
								</table>
						</div>
					</div>

					<?php
                    $tr = '';
                    $num = 0;
                    if (!empty($this->item['payment_lists'])) {
                        foreach ($this->item['payment_lists'] as $key => $value) {
                            ++$num;

                            $date = '<div>'.date('d/m/Y', strtotime($value['date'])).'</div>';
                            $date .= '<div>'.date('H:i', strtotime($value['time'])).'</div>';

                            $bankDate = '-';
                            if ($value['bank_date'] != '0000-00-00') {
                                $bankDate = date('d/m/Y', strtotime($value['bank_date'].' 00:00:00'));
                            }

                            $type = '';
                            if (!empty($value['type_is_cash'])) {
                                $type = $value['type_name'];
                            } elseif (!empty($value['type_is_bank'])) {
                                $type = '<div>'.$value['type_name'].'</div>';
                                $type .= '<div>('.$value['bank_code'].') '.$value['account_number'].'</div>';
                            } elseif (!empty($value['type_is_check'])) {
                                $type = '<div>'.$value['type_name'].'</div>';
                                $type .= '<div>'.$value['bank_code'].'-'.$value['check_number'].'</div>';
                            }

                            $image = '-';
                            if (!empty($value['image_arr'])) {
                                $image = '<a href="'.URL.'payments/showPicture/'.$value['id'].'" target="_blank" data-plugins="dialog"><i class="icon-eye"></i></a>';
                            }

                            $tr .= '<tr>'.
                                        '<td class="tac pas">'.$num.'</td>'.
                                        '<td class="tac pas">'.$date.'</td>'.
                                        '<td class="tac pas">'.$type.'</td>'.
                                        '<td class="tac pas">'.$bankDate.'</td>'.
                                        '<td class="pas">'.(number_format($value['amount'], 2)).'</td>'.
                                        '<td class="tac">'.$image.'</td>'.
                                        '<td class="pas whitespace">';
                            // if ($this->item['net_price'] == $this->item['pay']) {
                            //     $tr .= '<span class="gbtn">
                            // 				<a href="javascript:;" class="btn btn btn-no-padding btn-orange disabled"><i class="icon-pencil"></i>
                            // 				</a>
                            // 			</span>
                            // 			<span class="gbtn">
                            // 				<a href="javascript:;" class="btn btn btn-no-padding btn-red disabled"><i class="icon-trash"></i>
                            // 				</a>
                            // 			</span>';
                            // } else {
                            $tr .= '<span class="gbtn">
											<a data-plugins="dialog" href="'.URL.'payments/edit/'.$value['id'].'" class="btn btn btn-no-padding btn-orange"><i class="icon-pencil"></i>
											</a>
										</span>
										<span class="gbtn">
											<a data-plugins="dialog" href="'.URL.'payments/del/'.$value['id'].'" class="btn btn btn-no-padding btn-red"><i class="icon-trash"></i>
											</a>
										</span>';
                            // }
                            $tr .= '</td>'.
                                    '</tr>';
                        }
                    }

                    $pay_cls = 'btn btn-no-padding btn-blue';
                    $pay_icon = 'icon-plus';
                    if (empty($this->item['balance'])) {
                        $pay_cls .= ' disabled';
                        $pay_icon = 'icon-lock';
                    }

                    if ($this->item['process']['id'] == 7) {
                        $pay_cls .= ' disabled';
                        $pay_icon = 'icon-lock';
                    }
                    ?>

					<div class="span6">
						<div class="uiBoxWhite pas pam">
							<!-- <div class="rfloat">
								<span class="gbtn">
									<a class="<?=$pay_cls; ?>" data-plugins="dialog" href="<?=URL; ?>payments/add/<?=$this->item['id']; ?>"><i class="<?=$pay_icon; ?>"></i></a>
								</span>
							</div>-->
							<div class="nfloat mts">
								<h3 class="fwb mbm"><i class="icon-money"></i> Payment transaction</h3>
									<table class="table-bordered" width="100%">
										<thead>
											<tr>
												<th class="pas">#</th>
												<th class="pas">Date</th>
												<th clsss="pas">Payment method</th>
												<th clsss="pas">Bank Date</th>
												<th class="pas">Amount</th>
												<th class="pas">Evidence</th>
												<th class="pas">Actions</th>
											</tr>
										</thead>
										<?php
                                            if (!empty($tr)) {
                                                ?>
										<tbody>
											<?=$tr; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="4" class="tar pas">Total</th>
												<th colspan="2" class="tac pas"><?=number_format($this->item['net_price'], 2); ?></th>
											</tr>
											<tr>
												<th colspan="4" class="tar pas">Paid</th>
												<th colspan="2" class="tac pas"><?= !empty($this->item['pay']) ? number_format($this->item['pay'], 2) : '-'; ?></th>
											</tr>
											<tr>
												<th colspan="4" class="tar pas">Outstanding</th>
												<th colspan="2" class="tac pas"><?= !empty($this->item['balance']) ? number_format($this->item['balance'], 2) : '-'; ?></th>
											</tr>
										</tfoot>
										<?php
                                            } else {
                                                echo '<tbody>
													<tr>
														<td colspan="6" style="text-align:center;">
															<span class="fwb" style="color:red;">No payment information found.</span>
														</td>
													</tr>
												</tbody>';
                                            } ?>
									</table>
							</div>
						</div>

						<div class="uiBoxWhite pas pam mtl">
							<div class="rfloat">
								<span class="gbtn">
									<a class="btn btn-no-padding btn-blue" data-plugins="dialog" href="<?=URL; ?>events/add?obj_id=<?=$this->item['id']; ?>&obj_type=orders"><i class="icon-plus"></i></a>
								</span>
							</div>
							<div class="nfloat">
								<h3 class="fwb mbm"><i class="icon-calendar-check-o"></i> Appointment List</h3>
								<div ref="table" class="listpage2-table">
									<table class="table-bordered">
										<thead>
											<tr>
												<th class="ID">No.</th>
												<th class="date">Date</th>
												<th class="name">Topics</th>
												<th class="actions"></th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($this->events['lists'])) {
                                                $no = 0;
                                                foreach ($this->events['lists'] as $key => $value) {
                                                    ++$no;
                                                    $time = 'All day';
                                                    $start_time = date('H:i', strtotime($value['start']));
                                                    if ($start_time != '00:00') {
                                                        $time = $start_time;
                                                    } ?>
												<tr>
													<td class="ID"><?=$no; ?></td>
													<td class="date">
														<?=date('d/m/Y', strtotime($value['start'])); ?>
														(<?=$time; ?>)
													</td>
													<td class="name"><?=$value['title']; ?></td>
													<td class="actions whitespace">
														<span class="gbtn">
															<a href="<?=URL; ?>events/edit/<?=$value['id']; ?>" class="btn btn-no-padding btn-orange" data-plugins="dialog"><i class="icon-pencil"></i></a>
														</span>
														<span class="gbtn">
															<a href="<?=URL; ?>events/del/<?=$value['id']; ?>" class="btn btn-no-padding btn-red" data-plugins="dialog"><i class="icon-trash"></i></a>
														</span>
													</td>
												</tr>
												<?php
                                                }
                                            } else {
                                                echo '<tr>
												<td colspan="5" style="text-align:center;">
												<span class="fwb" style="color:red;">No appointment found.</span>
												</td>
												</tr>';
                                            } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>


					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('body').delegate('[data-name=project_id]', 'change', function(event) {

		$.post( Event.URL+ 'orders/updateProject', {
			id: $(this).data('id'),
			val: $(this).val()
		}, function(data, textStatus, xhr) {

			if( data.message ){

				Event.showMsg({text: data.message, load: 1, auto: 1});
			}
		}, 'json');
	});

	$(function(){
		// console.log('555');
		$(document).on('keyup', '#pay_comission_amount', function(){
			var commission = parseFloat($(this).val());
			var maxCommission = parseFloat($(document).find('input[name=total_comission]').val());
			if (commission > maxCommission) {
				$(':button[type="submit"]').prop('disabled', true);
				$('.error-commission').html('Commission more than limit');
				// $('#btn-save').prop('disabled', true);
				// console.log('Commission over ');
			} else {
				$(':button[type="submit"]').prop('disabled', false);
				$('.error-commission').html('');
				// $('#btn-save').attr('dissabled', false);
				// console.log('Commission OK');
			}

		})
	});
</script>