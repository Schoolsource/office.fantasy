<?php 

$this->format = !empty($this->format)? $this->format=='json': 'html';

if( $this->format=='json' ){

	echo json_encode(array('message'=>'Sorry, That page doesn\'t exist!','error'=>1));
}
else{

	echo '<div class="page-error-wrap"><div class="page-error-content">'.
		'<h1>Sorry, That page doesn\'t exist!</h1>'.
	'</div></div>';
}