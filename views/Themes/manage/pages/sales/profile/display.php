<div id="mainContainer" class="profile clearfix" data-plugins="main">
	<div class="setting-content" role="content">
		<div class="setting-main" role="main">

			<div class="clearfix pam">
				<div class="span12">
					<div class="setting-title">
						<i class="icon-users mrm"></i><?=$this->item['sale_fullname']?>
					</div>
					<div class="rfloat mrm">
						<span class="gbtn">
						<a class="btn btn-no-padding btn-orange" data-plugins="dialog" href="<?=URL?>sales/edit/<?=$this->item['id']?>?next=<?=URL?>customers"><i class="icon-pencil"></i></a>
					</span>
					<span class="gbtn">
						<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="<?=URL?>sales/del/<?=$this->item['id']?>?next=<?=URL?>customers"><i class="icon-trash"></i></a>
					</span>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div class="span12">
					<div class="uiBoxOverlay pam pas">
						<h3 class="mbm fwb"><i class="icon-user"></i> Information</h3>
						<ul>
							<li>
								<label><span class="fwb">Code : </span><?=$this->item['sale_code']?></label>
							</li>
							<li>
								<label><span class="fwb">Name : </span><?=$this->item['sale_fullname']?> (<?=$this->item['sale_name']?>)</label>
							</li>
							<li>
								<label><span class="fwb">Username : </span><?=$this->item['username']?></label>
							</li>
							<li>
								<label><span class="fwb">Region : </span><?=( !empty($this->item['region_arr']['name']) ? $this->item['region_arr']['name'] : "-" )?></label>
							</li>
							<li>
								<label><span class="fwb">Status : </span><?=(!empty($this->item['status_arr']['name']) ? $this->item['status_arr']['name'] : "-")?></label>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div class="span12">
					<div class="uiBoxOverlay mtm pam pas">
						<div>
							<ul>
								<li style="display:inline-block;">
									<label class="label fwb">Select Time</label>
									<select selector="closedate" name="closedate" class="inputtext"></select>
								</li>
								<li style="display: inline-block;">
									<div style="display: inline-block;" class="mll">
										<label class="radio fwb">
											<input type="radio" name="type" value="orders" checked="1">ยอดขาย
										</label>
									</div>
									<div style="display: inline-block;" class="mlm">
										<label class="radio fwb">
											<input type="radio" name="type" value="payment">ยอดเก็บเงิน
										</label>
									</div>
								</li>
								<li style="display:inline-block;" class="mlm">
									<span class="gbtn">
										<a class="js-search btn btn-green btn-no-padding"><i class="icon-search"></i></a>
									</span>
								</li>
							</ul>
						</div>
						<div id="saleMain" class="mtm"></div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
<script type="text/javascript">
	$('[selector=closedate]').closedate({
		leng:"th",
		options: [
			{
				text: 'This month',
				value: 'monthly',
			},
			{
				text: 'Custom',
				value: 'custom',
			}
		],
	});

	var id = <?=$this->item["id"]?>

	var loading = '<div class="tac"><div class="loader-spin-wrap" style="display:inline-block;"><div class="loader-spin"></div></div></div>';

	$("#saleMain").html( loading );
	$.get( Event.URL + 'sales/' + id, function(res){
		$("#saleMain").html( res );
	});

	$('.js-search').click(function(){
		$("#saleMain").html( loading );

		var start = $('[name=start_date]').val();
		var end = $('[name=end_date]').val();
		var type = $('[name=type]:checked').val();

		$.get( Event.URL + 'sales/' + id, {start:start, end:end, type:type}, function(res){
			$("#saleMain").html( res );
		});
	});
</script>
