<?php


echo '<!doctype html>';

if ($this->elem('html')->attr()) {
    $attributes = '';
    foreach ($this->elem('html')->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<html'.$attributes.'>';
} else {
    echo '<html>';
}

echo '<head>';

// Page title
/*if( $this->pageTitle=='Index' ){
    $this->pageTitle = $this->pageSiteName;
}*/

// Page title
echo '<title id="pageTitle">'.$this->getPage('title').'777</title>';
echo '<meta charset="utf-8" />';

/* set Touch Zooming  */
if ($this->fn->check_user_agent('mobile')) {
    $this->elem('body')->addClass('touch');
    echo '<meta name="viewport" content="user-scalable=no,initial-scale=1,maximum-scale=1">';
    // echo '<link rel="mask-icon" href="'.IMAGES.'favicon.svg">';
}
echo '<link rel="shortcut icon" href="'.IMAGES.'favicon.png">';
$_content = $this->getPage('color');
if (!empty($color)) {
    echo '<meta name="theme-color" content="'.$_content.'">';
}

/* Set Meta SEO */
$og = array(0 => 'title', 'url', 'site_name', 'description', 'keywords', 'image');
foreach ($og as $i => $val) {
    $_content = $this->getPage($val);

    if (!empty($_content)) {
        echo '<meta name="'.$val.'" content="'.$_content.'">';
    }
}

/* Set Meta Twitter SEO */
$og = array(0 => 'card', 'title', 'site', 'creator', 'description', 'image', 'domain');
foreach ($og as $i => $val) {
    $_content = $this->getPage($val);

    if (!empty($_content)) {
        echo '<meta name="twitter:'.$val.'" content="'.$_content.'">';
    }
}

/* Set Meta Google SEO */
$og = array(0 => 'title', 'site_name', 'url', 'description', 'type', 'keywords', 'locale', 'image');
foreach ($og as $i => $val) {
    $_content = $this->getPage($val);

    if (!empty($_content)) {
        echo '<meta property="og:'.$val.'" content="'.$_content.'">';
    }
}

$og = array(0 => 'type', 'width', 'height');
// Set Meta Image
foreach ($og as $i => $val) {
    $_content = $this->getPage("image_{$val}");

    if (!empty($_content)) {
        echo '<meta property="og:image:'.$val.'" content="'.$_content.'">';
    }
}

// Set Meta Facebook
$_content = $this->getPage('facebook_app_id');
if (!empty($_content)) {
    echo '<meta name="fb:app_id" content="'.$_content.'">';
    echo '<meta name="article:author" content="https://www.facebook.com/'.$_content.'">';
    echo '<meta name="article:publisher" content="https://www.facebook.com/'.$_content.'">';

    // fb.me
}

echo $this->head('css');
echo $this->head('js');
echo $this->head('style');

// <!--[if lt IE 10]>
// <script>var ie = true;</script>
// <![endif]-->

echo '<script type="text/javascript" src="/public/plugins/selectize/js/standalone/selectize.min.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="/public/plugins/selectize/css/selectize.bootstrap3.css" />';

echo '</head>';

if ($this->elem('body')->attr()) {
    $attributes = '';
    foreach ($this->elem('body')->attr() as $key => $value) {
        $attributes .= " {$key}=\"{$value}\"";
    }

    echo '<body'.$attributes.'>';
} else {
    echo '<body>';
}
