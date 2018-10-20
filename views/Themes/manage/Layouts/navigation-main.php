<?php

$url = URL;

$image = '';
if( !empty($this->me['image_url']) ){
	$image = '<div class="avatar lfloat mrm"><img class="img" src="'.$this->me['image_url'].'" alt="'.$this->me['fullname'].'"></div>';
}
else{
	$image = '<div class="avatar lfloat no-avatar mrm"><div class="initials"><i class="icon-user"></i></div></div>';
}

echo '<div class="navigation-main-bg navigation-trigger"></div><nav class="navigation-main" role="navigation"><a class="btn btn-icon navigation-trigger"><i class="icon-bars"></i></a>';

echo '<div class="navigation-main-header"><div class="anchor clearfix">'.$image.'<div class="content"><div class="spacer"></div><div class="massages"><div class="fullname">'.$this->me['name'].'</div><span class="subname">'.$this->me['dep_name'].'</span></div></div></div></div>';

echo '<div class="navigation-main-content">';
include WWW_VIEW. 'Layouts/navigation-main.php';
echo '</div>';

	echo '<div class="navigation-main-footer">';


// $image = $this->getPage('image');
// $image = !empty($image)	? '<img class="lfloat mrm" src="'.$image.'">': '';
$image = '<img class="lfloat mrm" src="http://fantasy.co.th/images/logo_site_1.jpg">';

echo '<ul class="navigation-list">'.

	'<li class="clearfix">'.
		'<div class="navigation-main-footer-cogs">'.
			'<a data-plugins="dialog" href="'.URL.'logout/admin"><i class="icon-power-off"></i><span class="visuallyhidden">Log Out</span></a>'.
			// '<a href="'.URL.'logout/admin"><i class="icon-cog"></i><span class="visuallyhidden">Settings</span></a>'.
		'</div>'.
		'<div class="navigation-brand-logo clearfix">'.$image.( !empty( $this->system['title'] ) ? $this->system['title']:'' ).'</div>'.
	'</li>'.
'</ul>';

echo '</div>';


echo '</nav>';