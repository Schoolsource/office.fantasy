<?php require_once 'init.php'; ?>

<div id="mainContainer" class="clearfix listpage2-container" data-plugins="main">

	<div role="content">
		<div role="main">

<div class="listpage2 has-loading offline listpage2-mg" data-plugins="listpage2" data-options="<?= $this->fn->stringify(array(
        'url' => $this->getURL,
    )); ?>">

	<!-- header -->
	<?php require 'header.php'; ?>

	<!-- table -->
	<div ref="table" class="listpage2-table table-mg">
		<div ref="tabletitle"><?php require 'tabletitle.php'; echo $tabletitle; ?></div>
		<div ref="tablelists"></div>

		<!-- <div class="listpage2-table-overlay"></div> -->
		<div class="listpage2-table-empty">
	        <div class="empty-icon"><i class="icon-cube"></i></div>
	        <div class="empty-title">Data not found.</div>
		</div>

	</div>

	<div class="listpage2-table-overlay-warp">
		<div class="listpage2-table-overlay"></div>
		<div class="listpage2-alert">
			<div class="listpage2-loading">
				<div class="listpage2-loading-icon loader-spin-wrap"><div class="loader-spin"></div></div>
				<div class="listpage2-loading-text">Loading...</div>
			</div>
		</div>
	</div>
</div>

		</div>
		<!-- end: main -->
	</div>
	<!-- end: content -->
</div>
<!-- end: container -->


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