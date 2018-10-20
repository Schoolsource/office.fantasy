<?php

require 'init.php';

?><div class="pal" style="max-width: 720px">

<div class="setting-header cleafix">
	<div class="setting-title"><?=$this->lang->translate('Profile')?></div>
    <nav class="setting-header-taps"><?php

    $max_width = '420px';

    foreach ($taps as $key => $value) {
    	
    	$active = $this->_tap == $value['id'] ? ' active':'';
    	echo '<a class="tap'.$active.'" href="'.URL.'settings/company/'.$value['id'].'">'.$value['name'].'</a>';

    	if( $this->_tap == 'dealer' ){
    		$max_width = '720px';
    	}
    }

    ?></nav>
</div>

<section class="setting-section" style="max-width: <?=$max_width?>"><?php 
	
	require "sections/{$this->_tap}.php";
?></section>

</div>