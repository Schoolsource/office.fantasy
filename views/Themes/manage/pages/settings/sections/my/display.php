<?php

require 'init.php';

?><div class="pal" style="max-width: 720px">

<div class="setting-header cleafix">
	<div class="setting-title">Profile</div>
    <nav class="setting-header-taps"><?php

    foreach ($taps as $key => $value) {
    	
    	$active = $this->_tap == $value['id'] ? ' active':'';
    	echo '<a class="tap'.$active.'" href="'.URL.'settings/my/'.$value['id'].'">'.$value['name'].'</a>';
    }

    ?></nav>
</div>

<section class="setting-section" style="max-width: 420px"><?php 
	
	require "sections/{$this->_tap}.php";
?></section>

</div>