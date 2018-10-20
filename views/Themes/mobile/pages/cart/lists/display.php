<div class="js-control">
	<?php 
	include("sections/category.php");
	?>
	<div id="subMenu"></div>
	<div role="main" class="list-products">
		<div id="productsLists">
			<div class="empty">
				<div class="empty-loader loader-spin-wrap">
					<div class="loader-spin"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$.get( Event.URL + 'mobile/listsProducts/', {cate:<?=$this->item['id']?>}, function(res) {
		$("#productsLists").html( res );
	});
	$('.scrollmenu').find('a[data-id=<?=$this->item['id']?>]').addClass('active');

	$('.js-control').delegate('.getProducts', 'click', function(){
		$('.scrollmenu').find('a.active').removeClass('active');
		$(this).addClass('active');

		$.get( Event.URL + 'mobile/subMenu/', {id:$(this).data('id')}, function(res){
			$("#subMenu").html( res );
			Event.Plugins( $('.subMenu') );
		});

		$.get( Event.URL + 'mobile/listsProducts/', {cate:$(this).data('id')}, function(res) {
			$("#productsLists").html( res );
		});
	});

	$('.js-control').delegate('.getProducts2', 'click', function(){
		$('.subscrollmenu').find('a.active').removeClass('active');
		$(this).addClass('active');

		$.get( Event.URL + 'mobile/listsProducts/', {cate:$(this).data('id')}, function(res) {
			$("#productsLists").html( res );
		});
	});
</script>