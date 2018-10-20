<div class="scrollmenu">
	<?php foreach ($this->category['lists'] as $key => $value) {
		echo '<a data-id="'.$value['id'].'" class="getProducts">'.$value['name_th'].'</a>';
	} ?>
</div>