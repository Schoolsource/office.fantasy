<?php


// $selector['display'] = '';
require 'tablelists.php';

echo json_encode( array(
	'settings' => $this->results,
	'body' => $table,
	// 'selector' => $selector
));