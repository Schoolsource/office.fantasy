<style type="text/css" media="screen">
	.mediaWrapper>*, .mediaWrapper>a>* {
		position: relative;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
</style>

<div class="row">
	<div class="lfloat mlm pal rfloat" role="search">
		<form class="form-search" action="" id="global-nav-search">
			<label class="visuallyhidden" for="search-query">Search query</label>
			<input class="search-input inputtext" type="text" placeholder="ค้นหาเลขที่ใบบิล / ชื่อลูกค้า" name="key" autocomplete="off" value="<?=(!empty($_GET['key']) ? $_GET['key'] : '')?>">
			<button type="submit" class="search-icon js-search-action" tabindex="-1"><i class="icon-search"></i><span class="visuallyhidden"></span>
			</button>
		</form>
	</div>
</div>

<div class="wrapper web-lists-wrap active posts" data-plugins="datalistsbox" data-options="<?=$this->fn->stringify( array('url' => URL.'mobile/orders'.(!empty($_GET['key']) ? '?key='.$_GET['key'] : '')) )?>">
	<ul class="ui-list clearfix" role=listsbox></ul>
	<div class="empty">
		<div class="empty-loader loader-spin-wrap"><div class="loader-spin"></div></div>
		<?php 
		if( !empty($_GET["key"]) ){
			echo '<div class="empty-text"></div>';
		}
		?>
		<div class="empty-error js-refresh"></div>
	</div>
	<div class="web-lists-more"><a class="btn btn-jumbo js-more">โหลดเพิ่ม</a></div>
</div>