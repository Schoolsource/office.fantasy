<div id="mainContainer" class="clearfix" data-plugins="main">

	<?php require 'sections/toolbar.php'; ?>
	<div class="clearfix">
		<div role="main" id="main_comission"></div>
	</div>

</div>

<script type="text/javascript">

	$.fn.extend(
	{
		loadMain: function(month, year){
			$.get(Event.URL + 'reports/comission', {month:month, year:year, main:1}, function(res){
				$('#main_comission').html( res );
				Event.plugins( $('#main_comission') );
			});
		}
	});

	$('.js-control').loadMain();

	$('.js-control').change(function(){
		var month = $(this).find('[name=month]').val();
		var year = $(this).find('[name=year]').val();

		$(this).loadMain(month, year);
	});
</script>