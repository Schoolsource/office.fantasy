<?php

echo '<!doctype html>';

if( $this->elem("html")->attr() ){

    $attributes = "";
    foreach ($this->elem("html")->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<html'.$attributes.'>';
}
else{
    echo '<html>';
}

echo '<head>';

// Page title
echo '<title id="pageTitle">'. $this->getPage('title') .'</title>';
echo '<meta charset="utf-8" />';

/* set Touch Zooming  */
if( $this->fn->check_user_agent('mobile') ){

    $this->elem('body')->addClass('touch');
    echo '<meta name="viewport" content="user-scalable=no,initial-scale=1,maximum-scale=1">';
    // echo '<link rel="mask-icon" href="'.IMAGES.'favicon.svg">';

}
echo '<link rel="shortcut icon" href="'.IMAGES.'favicon.png">';
$color = $this->getPage('color');
if( !empty($color) ){
    echo '<meta name="theme-color" content="'.$color.'">';
}


echo $this->head('css');
echo $this->head('js');
echo $this->head('style');

// <!--[if lt IE 10]>
// <script>var ie = true;</script>
// <![endif]-->

echo '</head>';

if( $this->elem("body")->attr() ){

    $attributes = "";
    foreach ($this->elem("body")->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<body'.$attributes.'>';
	
}
else{
    echo '<body>';
}
?>