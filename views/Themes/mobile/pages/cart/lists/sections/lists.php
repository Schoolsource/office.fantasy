<style type="text/css" media="screen">
	.mediaWrapper>*, .mediaWrapper>a>* {
		position: relative;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
</style>

<div class="row clearfix mtl">
	<div class="lfloat"><h3><i class="icon-cubes"></i> <?=$this->item['name_th']?></h3></div>
	<div class="rfloat" role="search">
		<form class="form-search" action="" id="global-nav-search">
			<label class="visuallyhidden" for="search-query">Search query</label>
			<input class="search-input inputtext" type="text" placeholder="ค้นหาสินค้า" name="key" autocomplete="off" value="<?=(!empty($_GET['key']) ? $_GET['key'] : '')?>">
			<button type="submit" class="search-icon js-search-action" tabindex="-1"><i class="icon-search"></i><span class="visuallyhidden"></span>
			</button>
		</form>
	</div>
</div>

<div class="wrapper web-lists-wrap active posts">
	<ul class="ui-list clearfix" role=listsbox>
		<?php 
		foreach ($this->results['lists'] as $key => $value) { 
			$image = !empty($value['image_url']) ? '<img src="'.$value['image_url'].'" />':'';
		?>
		<li class="ui-list-item border-bottom mhs pas anchor clearfix">
			<div class="mediaWrapper lfloat mrm"><?=$image?></div>
			<div class="rfloat  icon tac mrm">
				<span class="gbtn">
					<a class="btn btn-blue btn-jumbo" style="font-size: 30px; border-radius: 5px 5px 5px 5px">
						<i class="icon-info"></i>
					</a>
				</span>
			</div>
			<div class="content">
				
					<div class="massages">
					<div class="ui-score"></div>
					<div class="title fwb" style="font-size: 18px;"><?=$value['pds_name']?></div>
					<div class="fwb" style="font-size: 20px; color:red;"><?= !empty($value['pricing']['frontend']) ? number_format($value['pricing']['frontend']) : number_format($value['pds_price_frontend']) ?> ฿</div>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>