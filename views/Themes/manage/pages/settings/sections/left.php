<div class="settings-left" role="left" data-width="260">

	<!-- menu -->
	<div class="settings-nav">
	<?php foreach ($menu as $key => $value) { ?>
		<div>
			<a class="settings-nav-header-link<?php 
						if(!empty($value['key']) && !empty($this->section)){
							if( $value['key']==$this->section ){
								echo ' active';
							}
						} ?>"<?php if(!empty($value['url'])){
							echo '  href="'.$value['url'].'"';
						} ?>><?=$value['text']?></a>
		<?php if( !empty($value['sub']) ){ ?>
			<ul class="settings-nav-list">
				<?php foreach ($value['sub'] as $i => $item) { ?>
					<li><a class="settings-nav-page-link<?php 

						if( !empty($item['key']) && !empty($this->_tap) ){
							if( $item['key']==$this->section ){
								echo ' active';
							}

						}elseif( !empty($item['key']) && !empty($this->tap) ){
							if( $item['key']==$this->tap ){
								echo ' active';
							}
						}
						elseif(!empty($item['key']) && !empty($this->section)){
							if( $item['key']==$this->section ){
								echo ' active';
							}
						} ?>"<?php if(!empty($item['url'])){
							echo '  href="'.$item['url'].'"';
						} ?>><?=$item['text']?></a></li>
				<?php } ?> 
			</ul>
		<?php } ?>

		</div>
	<?php } ?>
	</div>
	<!-- /menu -->

</div>