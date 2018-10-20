<?php

$url = URL.'mobile/';

echo '<div class="navigation-main-bg js-navigation-trigger"></div>';

echo '<nav class="navigation-main" role="navigation">';

	// echo '<a class="btn btn-icon js-navigation-trigger"><i class="icon-bars"></i></a>';

echo '<div class="navigation-main-content">';

$image = '';
if( !empty($this->me['image_url']) ){
	$image = '<div class="avatar lfloat mrm"><img class="img" src="'.$this->me['image_url'].'" alt="'.$this->me['name'].'"></div>';
}
else{
	$image = '<div class="avatar lfloat no-avatar mrm"><div class="initials"><i class="icon-user"></i></div></div>';
}

echo '<div class="welcome-box"><div class="anchor clearfix">'.$image.'<div class="content"><div class="spacer"></div><div class="massages"><div class="fullname">'.( !empty($this->me['name']) ? $this->me['name'] : $this->me['sale_name'] ).'</div><span class="subname">'.( $this->me['type'] == 'sale' ? 'CODE : '.$this->me['sale_code'] : '' ).'</span></div></div></div></div>';

$info[] = array('key'=>'home','text'=>'Home','link'=>$url,'icon'=>'home');
// echo $this->fn->manage_nav($info, $this->getPage('on'));

#search
$menu1[] = array('key'=>'customers', 'text'=>'Customers', 'link'=>$url.'customers', 'icon'=>'users');
$menu1[] = array('key'=>'orders', 'text'=>'Orders', 'link'=>$url.'orders', 'icon'=>'cube');
echo $this->fn->manage_nav($menu1);

// $orders[] = array('key'=>'orders', 'text'=>'Orders', 'link'=>$url.'orders', 'icon'=>'cube');
// echo $this->fn->manage_nav($orders);

	echo '</div>';

	echo '<div class="navigation-main-footer">';


echo '<ul class="navigation-list"><li><a data-plugins="dialog" href="'.URL.'logout/admin?next='.URL.'"><i class="icon-power-off"></i>Logout</a></li></ul>';

echo '</div>';


echo '</nav>';